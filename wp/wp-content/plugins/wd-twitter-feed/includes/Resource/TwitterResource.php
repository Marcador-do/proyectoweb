<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */

namespace TwitterFeed\Resource;

/*----------------------------------------------------------------------------*\
 * The TwitterResource class contains methods required to perform a request
 * to the twitter API for a certain twitter resource.
 * Each TwitterResource child class represents one of twitter's resources
 * available through twitter API 1.1
\*----------------------------------------------------------------------------*/

/**
 * TwitterResource class
 */
abstract class TwitterResource 
{
    protected static $cache;
    protected $settings;
    protected $request_count;
    
    // Debug
    private $debug;
    
    // Admin options
    private $options;
    
    // URLs
    const API_URL = 'https://api.twitter.com/1.1/';
    
    /**
     * Constructor
     * @param    type    $settings    The resource settings
     *                                e.g. count, user, query, list etc...
     */
    public function __construct($settings) 
    {
        self::$cache = \TwitterFeed\Parser\Cache::get_instance();
        $this->settings = $settings;
        
        // Set the options
        global $twitterfeed_options;
        $this->options = $twitterfeed_options;
        
        // Debug
        $this->debug = \TwitterFeed\Debug\Debugger::get_instance();
        $this->debug->append('New ' . $this->settings['resource'] . ' resource created');
        
        // Initiate
        $this->init();
    }
    
    /**
     * Build Argument List
     * Returns an argument list that can be used with a GET request
     * NOTE: This function is overriden by the child class
     * 
     * @param    array    $args    An array of arguments
     * @return    String            The argument list encoded for GET request
     */
    protected function build_argument_list(array $args) 
    {
        $arg_list = '';
        foreach($args as $key => $value)
        {
            $arg_list = add_query_arg( array( $key => $value ), $arg_list );
        }
        return esc_url_raw( $arg_list );
    }
    
    /**
     * Friendly URL
     * Return a friendly URL from the given string
     * 
     * @param    String    $str    The string to encode
     * @return  String            The encoded URL
     */
    protected function friendly_url($str) 
    {
        return strtolower(preg_replace('/[^a-zA-Z0-9]+/','-',$str));
    }
    
    /**
     * Get the requested number of tweets
     * @return int The number of tweets
     */
    public function get_count() 
    {
        return $this->settings['count'];
    }
    
    /**
     * Get the number of times this request
     * has been made (for forcing tweet count 
     * purposes)
     * @return int The request count
     */
    public function get_request_count() 
    {
        return $this->request_count;
    }
    
    /**
     * Increment the request count
     */
    protected function increment_request_count() 
    {
        if($this->request_count == null)
        {
            $this->request_count = 1;
        }
        else $this->request_count++;
    }
    
    /**
     * Perform Request
     * 
     * Perform a twitter request for this resource
     * using the given tokens
     * 
     * @param    array    $tokens The tokens for the authenticating user
     * @return    array    json decoded response from tweeter    
     */
    public function perform_request($tokens) 
    {
    
        // Set the initial count for count forcing putposes
        if(!isset($this->initial_count)) $this->initial_count = $this->settings['count'];
        
        // Debug
        $this->debug->append('Performing a ' . $this->settings['resource'] . ' type request');
        $this->debug->append('Initial count: ' . $this->initial_count);
        $this->debug->append('arguments: ' . $this->build_argument_list());
        
        // Perform request
        $twitter = new \TwitterAPIExchange($tokens);
        $resp = $twitter->setGetfield($this->build_argument_list())
                        ->buildOauth($this->get_url(), 'GET')
                        ->performRequest();
        
        // Increment the requests count
        $this->increment_request_count();
        
        // Decode and filter the response
        $tweets = $this->filter_response(json_decode($resp));
    
        // Debug
        $this->debug->append('Received count: ' . count($tweets));
        
        // The "count" parameter behaves more closely in concept 
        // to an "up to" parameter in that you'll receive up to 
        // the number specified in the parameter. You cannot be 
        // guaranteed that you'll receive the total amount you've 
        // requested, even if the timeline contains at least that 
        // many accessible tweets.
        if(
            count($tweets) < $this->initial_count && 
            $this->options['enable_force_tweet_count'] == 'ON' && 
            $this->get_request_count() < $this->options['request_limit']
        ) {
            $this->settings['count'] += 20;
            $this->debug->append('Forcing tweet count with new count = ' . $this->settings['count']);
            $tweets = $this->perform_request($tokens);
        }

        return $tweets;
    }
    
    /**
     * Get the twitter api url corresponding to this resource type.
     * URL_SCRIPT must be defined in child class
     */
    public function get_url() 
    {
        return self::API_URL.static::URL_SCRIPT;
    }
    
    /*-----------------------------*/
    /* Abstract functions
    /*-----------------------------*/
    
    /**
     * Initiate
     * Automatically called by the constructor
     */
    abstract function init();
    
    /**
     * Is In Cache
     * 
     * Check if a certain resource can be found in cache before
     * making a new twitter request
     * 
     * @return    boolean    True if the number of tweets stored in 
     *                    cache for the requested resource is greater 
     *                    than or equal to the requsted number of tweets
     */
    abstract function is_in_cache();
    
    /**
     * Update the cache data for this resource type
     */
    abstract function update_cache_data($tweets);
    
    /**
     * Get the data stored in cache for this resource type
     */
    abstract function get_cached_data();
    
    /**
     * Filter the response from twitter
     * @param    array    $resp    The decoded response from twitter
     *                            Note: make sure you decode the response
     *                            which is returned as a string from twitter
     */
    abstract function filter_response($resp);
}