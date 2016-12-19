<?php
/**
 * Twitter Feed
 *
 * A powerful Twitter integration system that allows you to display tweets using widgets and shortcodes.
 *
 * @package   Twitter Feed
 * @author    Askupa Software <hello@askupasoftware.com>
 * @link      http://products.askupasoftware.com/twitter-feed/
 * @copyright 2016 Askupa Software
 *
 * @wordpress-plugin
 * Plugin Name:     Twitter Feed
 * Plugin URI:      http://products.askupasoftware.com/twitter-feed/
 * Description:     A powerful Twitter integration system that allows you to display tweets using widgets and shortcodes.
 * Version:         2.1.9
 * Author:          Askupa Software, ykadosh
 * Author URI:      http://www.askupasoftware.com
 * Text Domain:     twitterfeed
 * Domain Path:     /languages
 */

if( !function_exists('twitter_feed_bootstrap') )
{
    function twitter_feed_bootstrap()
    {
        $validator = require_once 'vendor/askupa-software/amarkal-framework/EnvironmentValidator.php';
        $validator->add_plugin( 'Twitter Feed', dirname( __FILE__ ).'/includes/TwitterFeed.php' );
    }
    twitter_feed_bootstrap();
}

if( !function_exists('twitter_feed_load_textdomain') )
{
    add_action( 'plugins_loaded', 'twitter_feed_load_textdomain' );
    function twitter_feed_load_textdomain() 
    {
        load_plugin_textdomain( 'twitterfeed', false, plugin_basename( dirname( __FILE__ ) ).'/languages/' );
    }
}