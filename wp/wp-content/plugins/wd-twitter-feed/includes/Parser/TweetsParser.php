<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */

namespace TwitterFeed\Parser;

/**
 * Tweets Parser
 * 
 * This class handles all the requests to Twitter as well as the caching and 
 * handling of the data retrieved from Twitter. This class follows the singleton 
 * design pattern.
 * 
 * About the caching algorithm:
 * This is a lazy algorithm. It only makes a request if one is needed, and it 
 * gets the minimum number of tweets that satisfy the user's demands.
 * When a page is loaded, this algorithm checks to see if the time since the 
 * last Twitter request is exceeding the time specified by the user. If it does, 
 * a request to Twitter will be made. If there is more than one TweetsView
 * object within a page, the algorithm will check to see if another request is
 * needed by first checking the cache data to see if the data is already 
 * available there.
 */
class TweetsParser 
{
    // Only instance
    private static $instance = null;
    
    private $tokens;                        // Twitter credentials
    private $options;                       // Plugin options
    private $enable_caching;                // True if the user has enabled caching
    private $caching_frequency;             // The user specified threshold to make a new request
    private $request_made = false;          // True if a request has been made during this run
    private $cache;                         // Cache object
    private $debug;                         // Debug data
    
    /**
     * Constructor
     */
    private function __construct()
    {
        // Get the cache instance
        $this->cache = Cache::get_instance();    
        
        // Set the options
        global $twitterfeed_options;
        $this->options = $twitterfeed_options;
        
        // Set the tokens
        $this->tokens = array(
            'oauth_access_token'        => $this->options['oauth_access_token'],
            'oauth_access_token_secret' => $this->options['oauth_access_token_secret'],
            'consumer_key'              => $this->options['consumer_key'],
            'consumer_secret'           => $this->options['consumer_secret']
        );

        // Set the caching variables
        if('ON' == $this->options['enable_caching']) {
            
            // Set the local variables
            $this->enable_caching = true;
            $this->caching_frequency = $this->options['caching_freq'];
            
            // Elapsed time from last call is greater 
            // than the specified caching frequency            
            if($this->cache->elapsed() > $this->caching_frequency)
            {
                // Clear the cache
                $this->cache->clear();
            }
        }
        
        // Hook the shutdown action
        add_action('shutdown', array($this, 'shutdown'));
        
        // Debug
        $this->debug = \TwitterFeed\Debug\Debugger::get_instance();
        $this->debug->append('Elapsed time: ' . $this->cache->elapsed());
        $this->debug->append('Caching Frequency: ' . $this->caching_frequency);
    }
    
    /**
     * Shutdown function
     * 
     * Called when PHP is about to end execution.
     * Handles the updating of the cache data and
     * time
     */
    public function shutdown()
    {
        if('ON' == $this->options['debug_mode'])
        {
            echo $this->debug->render();
        }
    }
    
    /**
     * Return an instance of this class
     * 
     * @return TweetsParser The only instance of this class
     */
    public static function get_instance() 
    {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    
    /**
     * Get the Tweets, either from Twitter.com
     * or from the cached data, if exits.
     * 
     * @return Tweets[] tweets array
     */
    public function getTweets( array $settings ) 
    {
        // No user was specified - bail early and throw an error
        if(($settings['resource'] == 'usertimeline' || $settings['resource'] == 'list') && !isset($settings['user']))
            throw new \Exception('No username was provided');
        
        // Must provide a search query for 'search' type resource
        if($settings['resource'] == 'search' && !isset($settings['query']))
            throw new \Exception('You must provide a search query when using "search" as a twitter resource');
        
        // Must provide a list name for 'list' type resource
        if($settings['resource'] == 'list' && !isset($settings['list']))
            throw new \Exception('You must provide a list name when using "list" as a twitter resource');
        
        // Must provide a list name for 'list' type resource
        if($settings['resource'] == 'single' && !isset($settings['id']))
            throw new \Exception('You must provide a tweet id when using "single" as a twitter resource');
        
        // No credentials were provided
        if( !$this->tokens['oauth_access_token'] || 
            !$this->tokens['oauth_access_token_secret'] ||
            !$this->tokens['consumer_key'] ||
            !$this->tokens['consumer_secret'])
            throw new \Exception('You did not provide the required Twitter tokens');
        
        // Fetch the feed
        $resp = $this->fetchFeed($settings);
        
        // Make sure Twitter returns some results
        if( 0 === count($resp) )
            throw new \Exception('Twitter API returned 0 results for your query.');
        
        return $resp;
    }
    
    /**
     * Fetch Feed
     * 
     * Fetches the feed and handles the errors. If the user 
     * has specified caching freuency, the function would 
     * claculate the elapsed time from last fetch and would 
     * use the last cached feed if the elapsed time is smaller 
     * than the specified chaching frequency. Otherwise, the 
     * function will make a request to Twitter.com to fetch 
     * the live data.
     * 
     * @return Tweets[] tweets array
     */
    private function fetchFeed(array $settings) 
    {
        // Create a new twitter resource
        $resource_class = '\\TwitterFeed\\Resource\\Resource_'.$settings['resource'];
        if( !class_exists($resource_class) )
        {
            throw new \RuntimeException('Invalid resource type: '.$settings['resource']);
        }
        $resource = new $resource_class($settings);

        // Debug
        $this->debug->append('Resource: ' . $settings['resource']);
        
        //-------------------
        // Caching is enabled
        //-------------------
        
        if($this->enable_caching) {
            $this->debug->append( 'Caching is enabled' );
            
            // Resource is not in cache
            if(!$resource->is_in_cache($settings['count'])) {
                
                // Debug
                $this->debug->append( 'Fetching new data' );
                                
                // Fetch new data
                $tweets = $this->toTweetArray($resource->perform_request($this->tokens));
                $resource->update_cache_data($tweets);

                // Notify that a request was made
                $this->request_made = true;
            }
            
            // Set the tweets
            $tweets = $resource->get_cached_data($settings['count']);
        }
        
        //--------------------
        // Caching not enabled
        //--------------------
        
        // Make a new request
        else {
            $this->debug->append( 'Caching not enabled' );
            $tweets = $this->toTweetArray($resource->perform_request($this->tokens));
        }

        // Get only the requested number of tweets
        $tweets = array_slice($tweets, 0, $settings['count']);
    
        // Return the Tweet[] array
        return $tweets;
    }
    
    /**
     * Convert Twitter 1.1 API data to an array of
     * Tweet objects
     * 
     * @param    array    $data    the data fetched from
     *                            Twitter.com
     * @throws    Exception        if the request returned errors
     * @return    Tweet            the Tweet array
     */
    private function toTweetArray( $data ) 
    {
        // Check for errors
        if( $data instanceof \stdClass && property_exists( $data, "errors" ) ) 
        {
            $message = "";
            foreach( $data->errors as $err )
            {
                $message .= $err->message . " (error code: " . $err->code . ").<br/>For more information, visit <a href=\"https://dev.twitter.com/overview/api/response-codes\">Error Codes & Responses</a>.";
            }
            
            throw new \Exception($message);
        }

        // Tweet[] array
        $tweets = array();
        $this->debug->append('crude tweets = ' . print_r($tweets, true));

        // Each tweet in the array
        foreach( $data as $key ) 
        {    
            // Take retweeted info if this is a retweet
            if( isset($key->retweeted_status) ) 
            {
                // Get retweeter user name
                $retweeter = $key->user->name;
                
                // Replace tweet with retweeted status
                $key = $key->retweeted_status;
            } 
            else 
            {
                $retweeter = null;
            }
            
            // Process tweet text
            $tweet_text = $key->text;
            if( isset( $this->options['url_type'] ) && $this->options['url_type'] === 'expanded' )
            {
                $tweet_text = $this->expand_urls( $key->entities->urls, $tweet_text );
            }
            $tweet_text = utf8_encode( $tweet_text ); // Tweet text may contain special characters
            
            
            // Create a new Tweet object
            $tweet = new Tweet(array(
                'created_at'    => $key->created_at, 
                'image_url'     => $key->user->profile_image_url_https, 
                'screen_name'   => $key->user->screen_name, 
                'user_name'     => $key->user->name, 
                'tweet_text'    => $tweet_text, 
                'id_str'        => $key->id_str,
                'retweeter'     => $retweeter,
                'retweet_count' => $this->number_format($key->retweet_count),
                'favorite_count'=> $this->number_format($key->favorite_count),
                'media'         => $this->extract_media($key)
            ));
            
            // Add it to the collection
            $tweets[] = $tweet;
        }

        return $tweets;
    }
    
    private function extract_media( $tweet )
    {
        $media = array();
        // Images
        if(isset($tweet->entities->media))
        {
            foreach($tweet->entities->media as $_media) {
                $media[] = array(
                    'id' => $_media->id_str, // ID is used to check later if this is a thumbnail of a vine/video/gif and if so, remove it
                    'type' => $_media->type,
                    'url' => $_media->media_url_https,
                    'width' => $_media->sizes->large->w,
                    'height' => $_media->sizes->large->h
                );
            }
        }

        // Animated GIF
        if(isset($tweet->extended_entities->media))
        {
            foreach($tweet->extended_entities->media as $_media) {
                if($_media->type === 'animated_gif')
                {
                    $this->remove_medium( $media, $_media->id_str );
                    $media[] = array(
                        'type' => 'animated_gif',
                        'url' => $_media->video_info->variants[0]->url,
                        'width' => $_media->sizes->large->w,
                        'height' => $_media->sizes->large->h
                    );
                }
            }
        }

        // Vine & YouTube
        if(isset($tweet->entities->urls))
        {
            foreach($tweet->entities->urls as $url) 
            {
                // Vine
                if(strpos($url->display_url, 'vine.co') === 0)
                    $media[] = array(
                        'type' => 'vine',
                        'url' => $url->expanded_url,
                        'embed_url' => $url->expanded_url.'/embed/simple'
                    );

                // YouTube
                if(strpos($url->display_url, 'youtu.be') === 0)
                    $media[] = array(
                        'type' => 'youtube',
                        'url' => $url->expanded_url,
                        'embed_url' => 'https://www.youtube.com/embed/'.preg_replace('/https?:\/\/youtu\.be\//', '', $url->expanded_url)
                    );
            }
        }
        return $media == array() ? null : $media;
    }
    
    private function remove_medium( &$media, $id )
    {
        foreach( $media as $key => $medium )
        {
            if( $medium['id'] === $id )
            {
                unset($media[$key]);
                break;
            }
        }
    }
    
    private function number_format( $number )
    {
        $k = $number/1000;
        if( $k >= 1 ) return ((float)number_format($k,1)).'K';
        return $number;
    }
    
    private function expand_urls( $urls, $text )
    {
        foreach( $urls as $url )
        {
            $text = str_replace( $url->url, $url->expanded_url, $text );
        }
        return $text;
    }
}
