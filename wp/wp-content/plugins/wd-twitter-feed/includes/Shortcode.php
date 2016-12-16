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
 * Shortcodes
 * 
 * This class loads and registers all the twitter feed shortcodes.
 * The class follows the singleton design pattern.
 */
class Shortcode
{
    /**
     * Private constructor to prevent instantiation
     */
    private function __constract() {}
    
    /**
     * Register all the shortcodes to be used
     * by wordpress
     */
    public static function register() 
    {
        add_shortcode( 'statictweets', array( __CLASS__, 'statictweets' ));
        add_shortcode( 'scrollingtweets', array( __CLASS__, 'scrollingtweets' ));
        add_shortcode( 'slidingtweets', array( __CLASS__, 'slidingtweets' ));
    }
    
    /**
     * Static Tweet List shortcode
     */
    public static function statictweets( $atts ) 
    {
        // Do the shortcode
        return static_tweets( self::parse_atts( $atts ) );
    }
    
    /**
     * Scrolling Tweet List shortcode
     */
    public static function scrollingtweets( $atts ) 
    {
        // Do the shortcode
        return scrolling_tweets( self::parse_atts( $atts ) );
    }
    
    /**
     * Sliding Tweet List shortcode
     */
    public static function slidingtweets( $atts ) 
    {
        // Do the shortcode
        return sliding_tweets( self::parse_atts( $atts ) );
    }
    
    /**
     * 
     * @param type $atts
     * @return type
     */
    public static function parse_atts( $atts )
    {
        $atts['retweets'] = isset($atts['retweets']) && $atts['retweets'] == 'on';
        $atts['replies']  = isset($atts['replies']) && $atts['replies'] == 'on';
        $atts['show']     = isset($atts['show']) ? explode( ',', $atts['show'] ) : array();
        return $atts;
    }
}