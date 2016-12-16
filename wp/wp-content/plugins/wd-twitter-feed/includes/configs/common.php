<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */


/**
 * This file contains the UI components configuration for both the editor and the widgets.
 */

use Amarkal\UI;

$common = array(
    'user' => new UI\Components\Text(array(
        'name'          => 'user',
        'title'         => 'User Name',
        'description'   => 'Enter a Twitter.com user name',
        'help'          => 'This field is only applicable if the twitter resource type is set to "User Timeline" or "List". Enter the Twitter accout username of which you would like to display the tweets',
    )),
    'list' => new UI\Components\Text(array(
        'name'          => 'list',
        'title'         => 'List Name',
        'description'   => 'Enter a Twitter.com list name',
        'help'          => 'This field is only applicable if the twitter resource type is set to "List".'
        ,'placeholder'  => 'Not available in the demo version',
        'disabled'      => true
    )),
    'id' => new UI\Components\Text(array(
        'name'          => 'id',
        'title'         => 'Tweet ID',
        'description'   => 'Enter a single Tweet ID',
        'help'          => 'This field is only applicable if the twitter resource type is set to "single".'
        ,'placeholder'  => 'Not available in the demo version',
        'disabled'      => true
    )),
    'query' => new UI\Components\Text(array(
        'name'          => 'query',
        'title'         => 'Search Query',
        'description'   => 'Enter a search query',
        'help'          => 'This field is only applicable if the twitter resource type is set to "Search". For more information about the query syntax, visit Twitter\'s search guide: https://dev.twitter.com/docs/using-search',
        'placeholder'   => 'from:username AND #hashtag OR @username'
        ,'placeholder'  => 'Not available in the demo version',
        'disabled'      => true
    )),
    'resource' => new UI\Components\DropDown(array(
        'name'          => 'resource',
        'title'         => 'Resource',
        'description'   => 'Choose the twitter resource type',
        'default'       => 'usertimeline',
        'options'       => array(
            'usertimeline' => 'User Timeline'
        )
    )),
    'count' => new UI\Components\Spinner(array(
        'name'          => 'count',
        'title'         => 'Count',
        'description'   => 'Number of tweets to display',
        'min'           => 1,
        'default'       => 5
        ,'max'          => 20 
    )),
    'retweets' => new UI\Components\ToggleButton(array(
        'name'          => 'retweets',
        'title'         => 'Include Retweets',
        'default'       => 'on',
        'labels'        => array( 'on' => 'ON', 'off' => 'OFF' )
    )),
    'replies' => new UI\Components\ToggleButton(array(
        'name'          => 'replies',
        'title'         => 'Include Replies',
        'default'       => 'on',
        'labels'        => array( 'on' => 'ON', 'off' => 'OFF' )
    )),
    'show' => new UI\Components\Checkbox(array(
        'name'          => 'show',
        'title'         => 'Show options',
        'help'          => 'Select which tweet assets you would like to show',
        'default'       => 'username,screenname,avatar,time,actions,media',
        'options'       => array(
            'username'      => 'User Name',
            'screenname'    => 'Screen Name',
            'avatar'        => 'Avatar',
            'time'          => 'Tweet Time',
            'actions'       => 'Tweet Actions',
            'media'         => 'Media'
        )
    ))
);

return array(
        
    /**
     * Static Tweets
     */
    'statictweets' => array(
        new UI\Components\DropDown(array(
            'name'          => 'skin',
            'title'         => 'Skin',
            'description'   => 'Choose how to skin the tweets',
            'default'       => 'default',
            'options'       => array(
                'default'      =>'Default',
                'simplistic'   =>'Simplistic'
            )
        )),
        $common['resource'],
        $common['user'],
        $common['list'],
        $common['query'],
        $common['id'],
        $common['count'],
        $common['retweets'],
        $common['replies'],
        new UI\Components\ToggleButton(array(
            'name'          => 'ajax',
            'title'         => 'Load More Button',
            'default'       => 'off',
            'labels'        => array( 'on' => 'ON', 'off' => 'OFF' )
            ,'description'  => 'Not available in the demo version',
            'disabled'      => true
        )),
        $common['show']
    ),

    /**
     * Scrolling Tweets
     */
    'scrollingtweets' => array(
        new UI\Components\Spinner(array(
            'name'          => 'scroll_time',
            'title'         => 'Scrolling Time',
            'description'   => 'Set the duration of each slide in seconds',
            'min'           => 5,
            'max'           => 120,
            'step'          => 1,
            'default'       => 10
        )),
        $common['resource'],
        $common['user'],
        $common['list'],
        $common['query'],
        $common['id'],
        $common['count'],
        $common['retweets'],
        $common['replies'],
        new UI\Components\DropDown(array(
            'name'          => 'skin',
            'title'         => 'Skin',
            'description'   => 'Choose how to skin the tweets',
            'help'          => 'Note: for the LED Screen skin, use a longer scrolling time since the font size is bigger and it takes longer to scroll through the tweet',
            'default'       => 'default',
            'options'       => array(
                'default'      => 'Default',
                'led-screen'   => 'LED Screen'
            )
        ))
    ),

    /**
     * Sliding Tweets
     */
    'slidingtweets' => array(
        new UI\Components\DropDown(array(
            'name'          => 'slide_dir',
            'title'         => 'Slide Direction',
            'description'   => 'Set the direction of the slide',
            'default'       => 'right',
            'options' => array(
                'up'        => 'Up',
                'down'      => 'Down',
                'left'      => 'Left',
                'right'     => 'Right',
                'random'    => 'Random'
            )
        )),
        new UI\Components\Spinner(array(
            'name'          => 'slide_duration',
            'title'         => 'Slide Duration',
            'description'   => 'Set the duration of each slide in seconds',
            'min'           => 2,
            'max'           => 120,
            'step'          => 1,
            'default'       => 5
        )),
        $common['resource'],
        $common['user'],
        $common['list'],
        $common['query'],
        $common['id'],
        $common['count'],
        $common['retweets'],
        $common['replies'],
        new UI\Components\DropDown(array(
            'name'          => 'skin',
            'title'         => 'Skin',
            'description'   => 'Choose how to skin the tweets',
            'default'       => 'default',
            'options'       => array(
                'default'      =>'Default',
                'simplistic'   =>'Simplistic'
            )
        )),
        $common['show']
    )
);
