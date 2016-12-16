<?php
/**
 * @package    twitterfeed
 * @date       Thu Aug 18 2016 17:02:02
 * @version    2.1.9
 * @author     Askupa Software <hello@askupasoftware.com>
 * @link       http://products.askupasoftware.com/twitter-feed/
 * @copyright  2016 Askupa Software
 */

use Amarkal\Extensions\WordPress\Options;
use Amarkal\UI\Components;

return array(
    'banner'        => TwitterFeed\IMG_URL.'/banner.jpg',
    'title'         => 'Twitter Feed',
    'subtitle'      => 'A powerful Twitter integration system that allows you to display tweets using widgets and shortcodes.',
    'version'       => 'v'.TwitterFeed\PLUGIN_VERSION,
    'author'        => 'Askupa Software',
    'author_url'    => 'http://www.website.com',
    'sidebar_title' => 'Twitter Feed',
    'sidebar_icon'  => 'dashicons-twitter',
    'footer_icon'   => TwitterFeed\IMG_URL.'/askupa-logo.png',
    'footer_text'   => date("Y").' © Askupa Software',
    'subfooter_text'=> 'If you like Twitter Feed, please give it <a target="_blank" href="https://wordpress.org/support/view/plugin-reviews/wd-twitter-feed?filter=5#postform">★★★★★</a> rating on WordPress.com. Thanks!',
    'global_variable'=> 'twitterfeed_options',
    'sections'      => array(
        new Options\Section(array(
            'title'         => 'Tokens',
            'icon'          => 'fa-list-ol',
            'description'   => 'Set your Twitter application access tokens. These access tokens are used by Twitter to authenticate the connection between your website to Twitter.com using API 1.1.<br/>Lost? read the tutorial on <a target="_blank" href="http://blog.askupasoftware.com/how-to-create-a-twitter-application/">How to create a twitter application</a>.',
            'subsections'   => array(
                array(
                    'title'     => 'Access Tokens',
                    'fields'    => array(
                        new Components\Text(array(
                            'name'      => 'oauth_access_token',
                            'title'     => 'Access Token',
                            'default'   => '',
                            'filter'    => function( $v ) { return trim($v); }
                        )),
                        new Components\Text(array(
                            'name'      => 'oauth_access_token_secret',
                            'title'     => 'Access Token Secret',
                            'default'   => '',
                            'filter'    => function( $v ) { return trim($v); }
                        )),
                        new Components\Text(array(
                            'name'      => 'consumer_key',
                            'title'     => 'Consumer Key (API Key)',
                            'default'   => '',
                            'filter'    => function( $v ) { return trim($v); }
                        )),
                        new Components\Text(array(
                            'name'      => 'consumer_secret',
                            'title'     => 'Consumer Secret (API Secret)',
                            'default'   => '',
                            'filter'    => function( $v ) { return trim($v); }
                        ))
                    )
                ),
                array(
                    'title'     => 'Usage Status',
                    'fields'    => array(
                        new Components\Content(array(
                            'template'   => \admin_url('admin-ajax.php'),
                            'full_width' => true,
                            'ajax'       => true,
                            'args'       => array(
                                'action'    => 'atf_usage_status'
                            ),
                            'callout'    => function() {
                                add_action( 'wp_ajax_atf_usage_status', function() {
                                    include( \TwitterFeed\PLUGIN_DIR . '/configs/options/usage-status.phtml' );
                                });
                            }
                        ))
                    )
                )
            ),
            
        )),
        new Options\Section(array(
            'title'         => 'Caching',
            'icon'          => 'fa-dashboard',
            'description'   => 'Setup how Twitter Feed caches and fetches tweets from Twitter.com',
            'fields' => array(
                new Components\Composite(array(
                    'name'          => 'force_tweet_count',
                    'title'         => 'Force Tweet Count',
                    'help'          => 'Make sure that the number of tweets recieved from Twitter.com matches the requested number of tweets. The "Request Limit" refers to the maximum number of requests allowed in case Twitter has returned a wrong number of tweets',
                    'template'      => 'none',
                    'components'    => array(
                        new Components\ToggleButton(array(
                            'name'          => 'enable_force_tweet_count',
                            'title'         => 'Enable',
                            'default'       => 'OFF',
                            'help'          => 'Twitter will not always return the requested number of tweets, since the tweet "count" parameter behaves more closely in concept to an "up to" parameter. Enable this option if you want to ensure that you get the right number of tweets. Notice that enabling this option can increase the network request time as multiple request will be made to Twitter.com until the requested count is returned. It is highly recommanded that you enable caching if you choose to enable this option.'
                        )),
                        new Components\Spinner(array(
                            'name'          => 'request_limit',
                            'title'         => 'Request Limit',
                            'default'       => 3,
                            'min'           => 1,
                            'max'           => 5,
                            'step'          => 1,
                            'help'          => 'set the maximum number of requests in case Twitter has returned a wrong number of tweets'
                        ))
                    )
                )),
                new Components\ToggleButton(array(
                    'name'        => 'enable_caching',
                    'title'       => 'Enable Caching',
                    'default'     => 'OFF',
                    'help'        => 'Use this option if you want to avoid sending a query to Twitter.com on every page reload. This is useful if you have a high traffic website and you wish to avoid reaching Twitter\'s query limits. Note that this can dramatically decrese the time it takes the server to return a response, since the data will be fetched from the local database, rather than from Twitter.com which is a remote address. For more information about Twitter rate limits, visit https://dev.twitter.com/docs/rate-limiting/1.1'
                )),
                new Components\Slider(array(
                    'name'       => 'caching_freq',
                    'title'      => 'Caching Frequency',
                    'default'    => 25,
                    'min'        => 10,
                    'max'        => 3600,
                    'step'       => 5,
                    'help'       => 'Set the caching frequency in seconds. This is used as the minimum threshold to make a query to Twitter.com (the minimum time between each query)'
                )),
                new Components\Process(array(
                    'name'       => 'clear_cache',
                    'title'      => 'Clear Cache',
                    'label'      => 'Clear Cache',
                    'callback'   => function() {
                        $cache = TwitterFeed\Parser\Cache::get_instance();
                        $cache->clear();
                        Options\Notifier::success('The cache has been successfully cleared.');
                    },
                    'help'      => 'Use this button to clear all the data currently stored in the cache'
                )),
                new Components\ToggleButton(array(
                    'name'      => 'debug_mode',
                    'title'     => 'Debug Mode',
                    'default'   => 'OFF',
                    'help'      => 'Enable/disable debug mode. This will display a debug window in the main website with useful information about the last run of the plugin.'
                )),
            )
        )),
        new Options\Section(array(
            'title'         => 'Appearance',
            'icon'          => 'fa-paint-brush',
            'description'   => 'Setup the look and feel of Twitter Feed',
            'fields'        => array(
                new Components\ToggleButton(array(
                    'name'          => 'url_type',
                    'title'         => 'URL Shortening',
                    'help'          => 'Choose between showing the short or the expanded version of the urls in the tweet text.',
                    'default'       => 'short',
                    'labels'        => [
                        'short'    => 'Short',
                        'expanded' => 'Expanded'
                    ]
                )),
                new Components\ToggleButton(array(
                    'name'          => 'expand_media',
                    'title'         => 'Expand Tweet Media',
                    'help'          => 'If set to "ON", tweet media will be expanded automatically when the page loads. Otherwise, media will be initially hidden, and a "Show Media" button will appear.',
                    'default'       => 'OFF'
                )),
                new Components\Spinner(array(
                    'name'          => 'avatar_size',
                    'title'         => 'Avatar Size',
                    'default'       => 32,
                    'step'          => 1,
                    'min'           => 0,
                    'help'          => 'Set the avatar width & height in pixels'
                )),
                new Components\ToggleButton(array(
                    'name'          => 'css_toggle',
                    'title'         => 'Use Custom CSS',
                    'help'          => 'Toggle on/off to use the custom CSS on the next field. The CSS code will be printed in the document\'s head',
                    'default'       => 'OFF'
                )),
                new Components\CodeEditor(array(
                    'name'          => 'css',
                    'title'         => 'Custom CSS Code',
                    'help'          => 'Insert your custom CSS here. Since this will be printed in the head of the document (as opposed to making an http request), it is not recommended to use this for big CSS changes (hundreds of lines of code).',
                    'language'      => 'css',
                    'default'       => "/**\n * Insert your custom CSS here\n */"
                ))
            )
        )),
        new Options\Section(array(
            'title'         => 'Help',
            'icon'          => 'fa-question-circle',
            'description'   => 'Extra information about the plugin, installation and usage instructions. Use the Contact Us section to submit a bug report or to leave feedback.',
            'subsections'   => array(
                array(
                    'title'         => 'How To Use',
                    'fields'        => array(
                        new Components\Content(array(
                            'template'      => \TwitterFeed\PLUGIN_DIR . '/configs/options/how-to-use.phtml',
                            'full_width'    => true
                        ))
                    )
                ),
                array(
                    'title'         => 'About',
                    'fields'        => array(
                        new Components\Content(array(
                            'template'      => \TwitterFeed\PLUGIN_DIR . '/configs/options/about-the-plugin.phtml',
                            'full_width'    => true
                        ))
                    )
                ),
                array(
                    'title'         => 'Contact Us',
                    'fields'        => array(
                        new Components\DropDown(array(
                            'name'      => 'contact_subject',
                            'title'     => 'What can we help you with?',
                            'options'   => array(
                                'I would like to report an error',
                                'I would like to request a new feature',
                                'I would like to leave a feedback',
                                'Other'
                            )
                        )),
                        new Components\Textarea(array(
                            'name'      => 'contact_message',
                            'title'     => 'Message',
                        )),
                        new Components\Process(array(
                            'name'          => 'submit_message',
                            'title'         => '',
                            'label'         => 'Submit',
                            'callback'      => function() {
                                $subject = filter_input(INPUT_POST, 'contact_subject');
                                $message = filter_input(INPUT_POST, 'contact_message');
                                $to      = "contact@askupasoftware.com";
                                $headers = 'From: webmaster@' . \preg_replace('/http\:\/\/(www\.)?/', '', \site_url() ) . "\r\n" .
                                           'Reply-To: ' .  \get_bloginfo( 'admin_email' ). "\r\n" .
                                           'X-Mailer: PHP/' . \phpversion();
                                $sent    = wp_mail( $to, $subject, $message, $headers );
                                
                                if( $sent )
                                {
                                    Options\Notifier::success('The email has been successfully sent.');
                                }
                                else
                                {
                                    Options\Notifier::error('An error has occured while trying to submit your email.');
                                }
                            }
                        ))
                    )
                )
            )
        ))
        ,new Options\Section(array(
            'title'         => 'Purchase',
            'icon'          => 'fa-shopping-cart',
            'description'   => 'Purchase the full version of Twitter Feed.',
            'fields' => array(
                new Components\Content(array(
                    'template'      => \TwitterFeed\PLUGIN_DIR . '/configs/options/purchase-full.phtml',
                    'full_width'    => true
                ))
            )
        ))
    )
);