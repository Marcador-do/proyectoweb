<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */

namespace TwitterFeed;

/**
 * The following functions are used as the application programming interface (API)
 * of Twitter Feed. Function names that are prepended with an underscore (_)
 * represent system functions that are only to be used internally.
 */

/**
 * Check if the cURL extension is installed
 * @return boolean
 */
function _is_curl_installed() 
{
    if( in_array('curl', get_loaded_extensions())) 
    {
        return true;
    }
    return false;
}

/**
 * Render an error message
 * 
 * @ignore
 * @since    1.43
 * 
 * @param    string    $message    The error message
 * @return   The HTML formatted error message    
 */
function _render_error( $message ) 
{
    ob_start();
    include( dirname( __FILE__ ) . '/Tweets/Error.phtml' );
    return ob_get_clean();
}

/**
 * Return an HTML view of static tweets.
 * 
 * @see http://products.askupasoftware.com/twitter-feed/api/
 * @api
 * @since 1.43
 * @return string The HTML formatted tweets.
 */
function static_tweets( $options ) 
{
    $parser = Parser\TweetsParser::get_instance();

    // Get the tweets
    try {
        $tweets = $parser->getTweets($options);
    }
    catch (\Exception $e) 
    {
        return _render_error( $e->getMessage() );
    }
    
    // Create a new tweet view
    $tweetview = new Tweets\UI\StaticTweets( $tweets, $options );

    // Get the html formatted list
    return $tweetview->render();
}

/**
 * Return an HTML view of scrolling tweets.
 * 
 * @see http://products.askupasoftware.com/twitter-feed/api/
 * @api
 * @since 1.43
 * @return        string    The HTML formatted tweets.
 */
function scrolling_tweets( $options ) 
{
    $parser = Parser\TweetsParser::get_instance();

    // Get the tweets
    try {
        $tweets = $parser->getTweets($options);
    } 
    catch (\Exception $e) 
    {
        return _render_error( $e->getMessage() );
    }
    
    // Create a new tweet view
    $tweetview = new Tweets\UI\ScrollingTweets( $tweets, $options );

    // Get the html formatted list
    return $tweetview->render();
}

/**
 * 
 * Return an HTML view of sliding tweets.
 * 
 * @see http://products.askupasoftware.com/twitter-feed/api/
 * @api
 * @since 1.43
 * @return        string    The HTML formatted tweets.
 */
function sliding_tweets( $options ) 
{
    $parser = Parser\TweetsParser::get_instance();

    // Get the tweets
    try 
    {
        $tweets = $parser->getTweets($options);
    } 
    catch (\Exception $e) 
    {
        return _render_error( $e->getMessage() );
    }
    
    // Create a new tweet view
    $tweetview = new Tweets\UI\SlidingTweets( $tweets, $options );

    // Get the html formatted list
    return $tweetview->render();
}