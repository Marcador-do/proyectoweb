<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */

namespace TwitterFeed\Widgets;

use Amarkal\UI\Components;

abstract class Widget
{
    public function register()
    {
        $widget = new \Amarkal\Extensions\WordPress\Widget\Widget( $this->get_config() );
        $widget->register(); 
    }
    
    public function get_config()
    {
        return new \Amarkal\Extensions\WordPress\Widget\WidgetConfig(array(
            'name'          => $this->get_name(),
            'version'       => \TwitterFeed\PLUGIN_VERSION,
            'languages-url' => \TwitterFeed\PLUGIN_DIR . 'languages/',
            'description'   => 'Use this widget to display tweets in your widget area',
            'callback'      => function( $args, $instance ) {

                extract($args, EXTR_SKIP);

                $title = apply_filters('widget_title', $instance['title']);

                // Display the widget wrapper and title 
                if ($instance['wrapper'] == 'ON')
                {
                    echo $before_widget;
                }

                if (!empty($title))
                {
                    echo $args['before_title'] . $title . $args['after_title'];
                }

                if (!empty($instance['subtitle']))
                {
                    echo $instance['subtitle'];
                }

                $instance['replies'] = $instance['replies'] == 'on';
                $instance['retweets'] = $instance['retweets'] == 'on';

                // Show is not set for ScrollingTweets
                if(isset($instance['show'])) 
                {
                    $instance['show'] = explode(',', $instance['show']);
                }
                
                // Render the tweets
                static::render( $instance );

                // Display the last part of the wrapper
                echo ( $instance['wrapper'] == 'ON' ? $after_widget : '' );
            }, 
            'fields' => static::get_components()
        ));
    }
    
    public function get_common_widget_components()
    {
        return array(
            new Components\Text(array(
                'name'          => 'title',
                'title'         => 'Widget Title',
                'default'       => 'My Tweets',
                'filter'        => function( $v ) { return trim( strip_tags( $v ) ); },
                'help'          => 'The widget\'s title appears at the top of the widget'
            )),
            new Components\Text(array(
                'name'          => 'subtitle',
                'title'         => 'Widget Subtitle',
                'placeholder'   => 'Some extra words...',
                'help'          => 'The widget\'s subtitle appears under the widget\'s title'
            )),
            new Components\ToggleButton(array(
                'name'          => 'wrapper',
                'help'          => 'Show/hide the theme default widget wrapper',
                'title'         => 'Display Widget Wrapper',
                'default'       => 'ON'
            ))
        );
    }
    
    public function get_common_tweet_ui_components( $type )
    {
        $common = include( dirname( __DIR__ ).'/configs/common.php' );
        return $common[$type];
    }
    
    public abstract function get_name();
    public abstract function get_components();
    public abstract function render( $instance );
}


