<?php
    /**
     * Plugin Name: WP RSS Aggregator - Excerpts and Thumbnails
     * Plugin URI: https://www.wprssaggregator.com/#utm_source=wpadmin&utm_medium=plugin&utm_campaign=wpraplugin
     * Description: Adds excerpts and thumbnails capability to WP RSS Aggregator.
     * Version: 1.10.1
     * Author: RebelCode
     * Author URI: https://www.wprssaggregator.com
     * Text Domain: wprss
     * Domain Path: /languages/
     * License: GPLv3
     */

    /**
     * Copyright (C) 2012-2016 RebelCode Ltd.
     *
     * This program is free software: you can redistribute it and/or modify
     * it under the terms of the GNU General Public License as published by
     * the Free Software Foundation, either version 3 of the License, or
     * (at your option) any later version.
     *
     * This program is distributed in the hope that it will be useful,
     * but WITHOUT ANY WARRANTY; without even the implied warranty of
     * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     * GNU General Public License for more details.
     *
     * You should have received a copy of the GNU General Public License
     * along with this program.  If not, see <http://www.gnu.org/licenses/>.
     */

use Aventura\Wprss\Core\Caching;


    /* Set the version number of the plugin. */
    if( !defined( 'WPRSS_ET_VERSION' ) )
        define( 'WPRSS_ET_VERSION', '1.10.1', true );

    /* Set the database version number of the plugin. */
    if( !defined( 'WPRSS_ET_DB_VERSION' ) )
        define( 'WPRSS_ET_DB_VERSION', '3' );

    /* Set constant path to the plugin directory. */
    if( !defined( 'WPRSS_ET_DIR' ) )
        define( 'WPRSS_ET_DIR', plugin_dir_path( __FILE__ ) );

    /* Set constant URI to the plugin URL. */
    if( !defined( 'WPRSS_ET_URI' ) )
        define( 'WPRSS_ET_URI', plugin_dir_url( __FILE__ ) );

    /* Set constant path to the main plugin file. */
    if( !defined( 'WPRSS_ET_PATH' ) )
        define( 'WPRSS_ET_PATH', __FILE__);

    /* Set the constant path to the plugin's CSS directory. */
    if( !defined( 'WPRSS_ET_CSS' ) )
        define( 'WPRSS_ET_CSS', WPRSS_ET_URI . trailingslashit( 'css' ), true );

    /* Set the constant path to the plugin's javascript directory. */
    if( !defined( 'WPRSS_ET_JS' ) )
        define( 'WPRSS_ET_JS', WPRSS_ET_URI . trailingslashit( 'js' ), true );

    /* Set the constant path to the plugin's includes directory. */
    if( !defined( 'WPRSS_ET_INC' ) )
        define( 'WPRSS_ET_INC', WPRSS_ET_DIR . trailingslashit( 'includes' ), true );

    // Set the constant path to the plugin's images directory.
    if( !defined( 'WPRSS_ET_IMG' ) )
        define( 'WPRSS_ET_IMG', WPRSS_ET_URI . trailingslashit( 'images' ), true );

    // Set the constant path to the plugin's images directory.
    if( !defined( 'WPRSS_ET_IMAGE_CACHE_TTL' ) )
        define( 'WPRSS_ET_IMAGE_CACHE_TTL', 60 * 60 * 24 * 7, true ); // 1 week

    define( 'WPRSS_ET_SL_STORE_URL', 'http://www.wprssaggregator.com/edd-sl-api/' );

    define( 'WPRSS_ET_SL_ITEM_NAME', 'Excerpts & Thumbnails' );


    /**
     * Load required files.
     */

    // Adding autoload paths
    add_action( 'plugins_loaded', function() {
	wprss_autoloader()->add('Aventura\\Wprss\\Ethumbnails', WPRSS_ET_INC);
    });

    // Load licensing loader file
    require_once ( WPRSS_ET_INC . 'licensing.php' );

    /* Load admin display file */
    require_once ( WPRSS_ET_INC . 'settings-initialize.php' );

    /* Load admin settings file */
    require_once ( WPRSS_ET_INC . 'admin-settings.php' );

    /* Load admin display file */
    require_once ( WPRSS_ET_INC . 'admin-display.php' );

	/* Load the feed display file */
    require_once ( WPRSS_ET_INC . 'feed-display.php' );

    /* Load the custom feed file */
    require_once ( WPRSS_ET_INC . 'custom-feed.php' );

    /* Load the word trimmer file */
    require_once ( WPRSS_ET_INC . 'word-trimmer.php' );

    register_activation_hook( __FILE__ , 'wprss_et_activate' );


    /**
     * Plugin activation procedure
     *
     * @since  1.0
     * @return void
     */
    function wprss_et_activate() {
        /* Prevents activation of plugin if compatible version of WordPress not found */
        if ( version_compare( get_bloginfo( 'version' ), '3.5', '<' ) ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            deactivate_plugins ( basename( __FILE__ ));     // Deactivate plugin
            wp_die( __( 'This plugin requires WordPress version 3.5 or higher.' ), 'WP RSS Aggregator Excerpts and Thumbnails', array( 'back_link' => true ) );
        }

        // Add the database version setting.
        update_option( 'wprss_et_db_version', WPRSS_ET_DB_VERSION );

        // Disabled because this would overrite the plugin settings every time there is a deactivation + activation
        /*
        update_option( 'wprss_settings_thumbnails', wprss_et_get_default_settings_thumbnails() );
        update_option( 'wprss_settings_excerpts', wprss_et_get_default_settings_excerpts() );
        */

        wprss_et_excerpts_settings_initialize();
        wprss_et_thumbnails_settings_initialize();
        wprss_et_licenses_settings_initialize();
        if ( function_exists( 'wprss_feed_reset' ) )
            wprss_feed_reset();
    }


    add_action( 'plugins_loaded', 'wprss_et_init' );
    /**
     * Initialize the module on plugins loaded, so WP RSS Aggregator should have set its constants and loaded its functions.
     * @since 1.0
     */
    function wprss_et_init() {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if ( ! defined( 'WPRSS_VERSION' ) ) {
            deactivate_plugins ( basename( __FILE__ ));     // Deactivate plugin
            add_action( 'all_admin_notices', 'wprss_et_missing_error' );
        }

        else if ( version_compare( WPRSS_VERSION, '4.8', '<' ) ) {
            deactivate_plugins ( basename( __FILE__ ));     // Deactivate plugin
            add_action( 'all_admin_notices', 'wprss_et_update_notice' );
        }

        /**
         * Load WPThumb
         * Needs to be loaded here else it will cause a conflict with the WPThumb plugin if installed
        */

        // Temporary workaround for WPTHumb redeclaration issue
        $active_plugins = get_option('active_plugins');
        $wpthumb_key = array_search( 'wp-thumb/wpthumb.php', $active_plugins );

        if ( !class_exists( 'WP_Thumb' ) && !function_exists( 'wpthumb' ) && $wpthumb_key === FALSE && // Check if the class and function do not exist and the plugin is not active
             !( isset( $_GET['plugin'] ) && $_GET['plugin'] == 'wp-thumb/wpthumb.php' && // And if the GET plugin and active values are not set
                isset( $_GET['action'] ) && $_GET['action'] == 'activate' )              // to the wp-thumb path and to activate, respectively
            ) {
            include_once ( WPRSS_ET_INC . 'libraries/WPThumb/wpthumb.php' );
        }

        do_action( 'wprss_et_init' );

        // Use the Excerpts & Thumbnails template for display rather than the default one
		if ( defined( 'WPRSS_VERSION' ) && version_compare( WPRSS_VERSION, '4.5.2', '<' ) ) {
        	remove_action( 'wprss_display_template', 'wprss_default_display_template', 10 );
		}
    }

    add_action( 'plugins_loaded', 'wprss_et_load_textdomain' );
    /**
     * Loads the plugin's translated strings.
     *
     * @since  1.8.6
     * @return void
     */
    function wprss_et_load_textdomain() {
        load_plugin_textdomain( WPRSS_TEXT_DOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }


    /**
     * Throw an error if WP RSS Aggregator is not installed.
     * @since 1.0
     */
    function wprss_et_missing_error() {
        $msg = sprintf(
            __('Please <a href="%s">install &amp; activate WP RSS Aggregator</a> for the Excerpts and Thumbnails addon to work.', WPRSS_TEXT_DOMAIN),
            esc_attr(admin_url( 'plugin-install.php?tab=search&type=term&s=wp+rss+aggregator&plugin-search-input=Search+Plugins' ))
            );
        echo '<div class="error"><p>' . $msg . '</p></div>';
    }


    /**
     * Throw an error if WP RSS Aggregator is not updated to the latest version
     * @since 1.0
     * @todo needs to be be localised
     */
    function wprss_et_update_notice() {
        echo '<div class="error"><p>' .
            __('Please update WP RSS Aggregator to the latest version for the Excerpts and Thumbnails addon to work properly.', WPRSS_TEXT_DOMAIN) .
            '</p></div>';
    }


    add_action( 'init', 'wprss_et_add_theme_support' );
    /**
     * Adds thumbnails support to the theme. Fixes bugs related to choosing the fallback
     * featured image per feed source.
     */
    function wprss_et_add_theme_support() {
        add_theme_support( 'post-thumbnails' );
    }


    add_action( 'admin_init', 'wprss_et_license_notification' );
    /**
     * Checks if a license code is entered. If not, shows a notification to remind the user.
     * Note: Does not check if the license code is valid!
     *
     * @since 1.2
     */
    function wprss_et_license_notification() {
        if ( function_exists( 'wprss_check_addon_notice_option' ) === FALSE ) {
            add_action( 'all_admin_notices', 'wprss_et_missing_error' );
            deactivate_plugins ( basename( __FILE__ ) );
            return;
        }
        $license_keys = get_option( 'wprss_settings_license_keys' );
        $et_license_key = ( isset( $license_keys['et_license_key'] ) ) ? $license_keys['et_license_key'] : '';
        $option = wprss_check_addon_notice_option();
        if ( strlen( $et_license_key) === 0 && isset( $option['excerpts_thumbnails']['license'] ) === FALSE && is_main_site() ) {
            add_action( 'all_admin_notices', 'wprss_et_license_notice' );
        }
    }


    /**
     * Prints the admin license notice
     *
     * @since 1.3.2
     */
    function wprss_et_license_notice() {
        $msg = sprintf(
            __('Remember to <a href="%s">enter your plugin license code</a> for the WP RSS Aggregator <b>Excerpts &amp; Thumbnails</b> add-on, to benefit from updates and support.', WPRSS_TEXT_DOMAIN),
            esc_attr(admin_url( 'edit.php?post_type=wprss_feed&page=wprss-aggregator-settings&tab=licenses_settings' ))
            );

        echo '<div class="updated">
                <p>' . $msg .
                    '<a href="#" class="ajax-close-addon-notice" style="float:right;" data-addon="excerpts_thumbnails" data-notice="license">' .
                    __('Dismiss this notification', WPRSS_TEXT_DOMAIN) . '</a>
                </p>
            </div>';
    }


    add_filter( 'wprss_populate_post_data', 'wprss_et_more_feed_item_data', 100, 2 );
    /**
     * Also store the the feed's description for use as excerpt
     * @since 1.0
     */
    function wprss_et_more_feed_item_data( $args, $item )
    {
        // $args['post_excerpt'] = $item->get_description();
        // Update: I think the right place is post_content, post_excerpt is for manually created excerpts
        // Also populating post_content although it's not being used for now
        // Not sure whether we should be using the post_excerpt or post_content for this plugin
        // so it might change in the future
        // Right now it doesn't really make any difference, they are just fields in the wp_post table
        $args['post_content'] = $item->get_content();
        // A developer might change this to $item->get_description by hooking into wprss_populate_data
        // at a higher priority. I'm still not 100% sure that we should use get_content() or get_description()
        // but we're going to try it like this and see how it goes.
        return $args;
    }


   // add_filter( 'wprss_feed_post_type_args', 'wprss_et_feed_default_thumbnail', 10 );
    /**
     * Also store the the feed's description for use as excerpt
     * @since 1.0
     */
    /*function wprss_et_feed_default_thumbnail( $args )
    {
    //      $args['post_excerpt'] = $item->get_description();

        return $args;
    }*/


    /**
     * Get the current post type from the WP Screen object, or POST AJAX request.
     *
     * @since 1.8
     * @return string Post type
     */
    function wprss_et_get_current_post_type() {
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
            if ( isset( $_REQUEST['post_id'] ) ) {
                $post = get_post( $_REQUEST['post_id'] );
                return $post->post_type;
            }
        }
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        $screen = get_current_screen();
        return $screen->post_type;
    }


    add_action( 'do_meta_boxes', 'wprss_et_feed_default_thumbnail_metabox');
    /**
     * Rename and move the featured image meta box
     * @since 1.0
     */
    function wprss_et_feed_default_thumbnail_metabox() {
        remove_meta_box( 'postimagediv', 'wprss_feed', 'side' );
        add_meta_box( 'postimagediv', __( 'Default thumbnail' ), 'post_thumbnail_meta_box', 'wprss_feed', 'side', 'low' );
    }


    add_filter( 'admin_post_thumbnail_html', 'wprss_custom_admin_post_thumbnail_html' );
    /**
     * Change "Featured Image" box Link Text
     * @since 1.5
     */
    function wprss_custom_admin_post_thumbnail_html( $content ) {
        if ( 'wprss_feed' === wprss_et_get_current_post_type() ) {
            if ( strstr( $content, __( 'Remove featured image' ) ) ) {
                return $content = str_replace( __( 'Remove featured image' ), __( 'Remove default thumbnail' ), $content);
            }
            if ( strstr( $content, __( 'Set featured image' ) ) )  {
                return $content = str_replace( __( 'Set featured image' ), __( 'Set default thumbnail' ), $content);
            }

            return $content;
        }
        else return $content;
    }


    add_filter( 'media_view_strings', 'wprss_change_media_view_strings' );
    /**
     * Change strings for the postimagediv metabox
     * @since 1.5
     */
    function wprss_change_media_view_strings( $strings ) {
        require_once(ABSPATH . 'wp-admin/includes/screen.php');
        $screen = get_current_screen();

        if ( ( 'wprss_feed' === $screen->post_type ) && ( 'post' === $screen->base ) ) {
            $strings['setFeaturedImageTitle'] = __( 'Set Default Thumbnail' );
            $strings['setFeaturedImage']    = __( 'Set default thumbnail' );
            return $strings;
        }
        else return $strings;
    }


    add_action('wprss_feed_post_type_args', 'wprss_et_add_thumbnail_support');
    /**
     * Enable thumbnail support for the wprss_feed_source post type
     *
     * @since 1.0
     */
    function wprss_et_add_thumbnail_support( $args ) {
        $args['supports'] = array( 'title', 'thumbnail' );
        return $args;
    }


    /**
     * Fetch thumbnail for the post
     * @since 1.0
     */
    function wprss_et_get_thumbnail_data() {
        $thumbnail = get_post_meta( get_the_ID(), 'wprss_item_thumbnail', true );
    }


    /**
     * Forces the use of the featured image.
     * This is the default behaviour: image is obtained from the feed. Users can force the
     * featured image by adding this filter.
     *
     * @since 1.5
     */
    function wprss_et_force_source_default_thumbnail( $force ) {
        return TRUE;
    }


    /**
     * Returns the first image found in the given item's content
     *
     * @since 1.8
     * @param $item (SimplePie_Item) The feed item
     * @return (string|null) Returns the string URL of the image found, or NULL if not image was found.
     */
    function wprss_et_first_image( $item ) {
        wprss_log( 'Getting first image from item content', NULL, WPRSS_LOG_LEVEL_SYSTEM );
        // Extract all images from the content into the $matches array
        preg_match_all( '/<img.*?src=[\'"](.*?)[\'"].*?>/xis', $item->get_content(), $matches );

        $count = isset( $matches[1][0] )? count( $matches[1][0] ) : 0;
        wprss_log( sprintf( 'Found %1$d image tags in post content', $count ), NULL, WPRSS_LOG_LEVEL_SYSTEM );

        // Loop images in feed item content ($matches) until a suitable one is found
        $i = 0;
        while ( !empty( $matches[1][$i] ) ) {
            // The current image found
            $image_found = urldecode( trim($matches[1][$i]) );
            // Add http prefix if not included
            if ( stripos( $image_found, '//' ) === 0 ) {
                $image_found = 'http:' . $image_found;
            }

            // FACEBOOK FIXES
            // Check if it is a facebook image url and if a larger size exists
            if ( stripos( $image_found, 'fbcdn' ) > 0 ){
                $ext = strrchr( $image_found, '.' );
                $fb_larger_img = str_replace( '_s' . $ext, '_n' . $ext , $image_found );
                // If the larger image exists, set the url to point to it
                if ( wprss_et_remote_file_exists( $fb_larger_img ) ){
                    $image_found = $fb_larger_img;
                }
            }
            // check if the URL is from 'fbexternal-a.akamaihd.net'
            // if so, we can use the url GET param to get the actual image url
            $image_url_host = parse_url( $image_found, PHP_URL_HOST );
            if ( $image_url_host === 'fbexternal-a.akamaihd.net' ) {
                wprss_log( sprintf( 'Detected "%1$s" image', $image_url_host ), NULL, WPRSS_LOG_LEVEL_SYSTEM );
                // Get the query string
                $query_str = parse_url( $image_found, PHP_URL_QUERY );
                // If not empty
                if ( $query_str !== '' ) {
                    // Parse it
                    parse_str( urldecode( $query_str ), $output );
                    wprss_log_obj( 'Parsed query str', $output, NULL, WPRSS_LOG_LEVEL_SYSTEM );
                    // If it has a url GET param, use it as the image URL
                    if ( isset( $output['amp;url'] ) ) $output['url'] = $output['amp;url'];
                    if ( isset( $output['url'] ) ) {
                        $image_found = urldecode( $output['url'] );
                        wprss_log( 'Found and using GET param "url"', NULL, WPRSS_LOG_LEVEL_SYSTEM );
                    }
                }
            }
            wprss_log( sprintf( 'Image: %1$s', $image_found ) , NULL, WPRSS_LOG_LEVEL_SYSTEM );

			if ( is_wp_error( $tmp_img = wprss_et_download_image( $image_found ) ) )
				return null;
			$dimensions = ($tmp = $tmp_img->get_local_path()) ? $tmp_img->get_size() : null;

            wprss_log( empty( $dimensions ) ? 'Dimensions could not be acquired' : sprintf( 'Dimensions: %1$dx%2$d', $dimensions[0], $dimensions[1] ), NULL, WPRSS_LOG_LEVEL_SYSTEM );
            $min_width = apply_filters( 'wprss_thumbnail_min_width', 50 );
            $min_height = apply_filters( 'wprss_thumbnail_min_height', 50 );

            // Try to eliminate smileys, emoticons and small icons from being set as thumbnails
            if ( $dimensions !== NULL && ( $dimensions[0] > $min_width ) && ( $dimensions[1] > $min_height ) ) {
                wprss_log( sprintf( 'Image found: %1$s', $image_found ), NULL, WPRSS_LOG_LEVEL_SYSTEM );
                return $image_found;
            }
            $i++;
        }
        wprss_log( 'No first-image found!', NULL, WPRSS_LOG_LEVEL_SYSTEM );
        return null;
    }


    /**
     * Returns the enclosure image for the given feed item.
     *
     * If multiple enclosure images existm the largest one is used.
     *
     * @since 1.8
     * @param $item (SimplePie_Item) The feed item
     * @return (string|null) The string URL of the image, or null if the item does not contain an enclosure image link.
     */
    function wprss_et_get_enclosure_image( $item ) {
         // Try to get image from enclosure if available
        $enclosure = $item->get_enclosure();

        if ( is_null( $enclosure ) ) return NULL;

        // Get all the thumbnails
        $all_thumbnails = (array) $enclosure->get_thumbnails();

        // If no thumbnails available, simply return NULL
        if ( count( $all_thumbnails ) === 0 ) {
            return NULL;
        }

        // Max size
        $max = array(
            'index'     =>  -1,
            'width'     =>  0,
            'height'    => 0
        );

        // Iterate the thumbnails
        foreach ( $all_thumbnails as $i => $thumbnail ) {
            // If null or empty, skip
            if ( is_null( $thumbnail ) && strlen( $thumbnail ) === 0 ) continue;

			// Get the image
			if ( is_wp_error( $image = wprss_et_download_image( $thumbnail ) )
					|| !$image
					|| !( $tmp = $image->get_local_path() ) )
				continue;

            // Get the size
            if ( $size = $image->get_size( $thumbnail ) ) {
                // If the size is greater than our current max, update max
                if ( $size[0] > $max['width']  && $size[1] > $max['height'] ) {
                    $max = array(
                        'index'     =>  $i,
                        'width'     =>  $size[0],
                        'height'    =>  $size[1]
                    );
                }
            }
        }

        // If the index is not -1
        if ( $max['index'] !== -1 ) {
            // Get the index
            $index = $max['index'];
            // Return the thumbnail at that index
            return $all_thumbnails[ $index ];
        }

        return NULL;
    }


    /**
     * Returns the <media:thumbnail> image for the given feed item.
     *
     * @since 1.8
     * @param $item (SimplePie_Item) The feed item
     * @return (string|null) The string URL of the image, or null if the item does not contain a <media:thumbnail> image.
     */
    function wprss_et_get_media_thumbnail_image( $item ) {
        // Try to get image from enclosure if available
        $enclosure = $item->get_enclosure();
        wprss_log( 'Got enclosure from feed', NULL, WPRSS_LOG_LEVEL_SYSTEM );
        if ( is_null( $enclosure ) ) return NULL;

        // get the enclosure thumbnail
		$link = $enclosure->get_link();
        if ( !is_null( $link )
				&& ($image = wprss_et_download_image( $link ) )
				&& (!is_wp_error( $image ))
				&& ($tmp = $image->get_local_path())) {
            wprss_log_obj( 'Valid! Returning link:', $link, NULL, WPRSS_LOG_LEVEL_SYSTEM );
            return $link;
        }

        wprss_log( 'Invalid!', NULL, WPRSS_LOG_LEVEL_SYSTEM );

        return NULL;
    }


    /**
     * Returns the auto-detected thumbnail for the given item
     *
     * @since 1.8
     * @param $item (SimplePie_Item) The feed item
     * @return (string|null) The string URL of the auto-detected image, or NULL if no image was found.
     */
    function wprss_et_auto_detect_thumbnail( $item ) {
        // Start with the media:thumbnail tag. It is usually the best image to use as
        // a thumbnail - IF it is available.
        // If found, return it
        $thumbnail = wprss_et_get_media_thumbnail_image( $item );
        if ( !is_null( $thumbnail ) ) {
            wprss_log_obj( 'Auto detected media:thumbnail image', $thumbnail, NULL, WPRSS_LOG_LEVEL_SYSTEM );
            return $thumbnail;
        }

        // If no media:thumbnail is found, try getting the first image,
        // if found, return it
        $first_image = wprss_et_first_image( $item );
        if ( !is_null( $first_image ) ) return $first_image;

        // Get the enclosure image, if available
        // if found, return it
        $enclosure = wprss_et_get_enclosure_image( $item );
        if ( !is_null( $enclosure ) ) return $enclosure;

        // Return null if no image was found
        return NULL;
    }


    add_action( 'wprss_items_create_post_meta', 'wprss_et_items_add_thumbnail_meta', '', 4 );
    /**
     * Fetch and save thumbnail for the feed item
     * @since 1.0
     */
    function wprss_et_items_add_thumbnail_meta( $inserted_ID, $item, $post_ID ) {

        $thumbnails_settings = get_option( 'wprss_settings_thumbnails' );
        $can_use_def_thumbnail = isset( $thumbnails_settings['use_def_thumbnail'] )? $thumbnails_settings['use_def_thumbnail'] : 'false';

        $width  = $thumbnails_settings['thumbnail_width'];
        $height = $thumbnails_settings['thumbnail_height'];

        /* Disabled these methods for now, too buggy, possibly old method of doing things


        if ( is_null( $thumbnail_img ) )
        {
            $media_group = $item->get_item_tags( '', 'enclosure' );
            $thumbnail_img = $media_group[0]['attribs']['']['url'];
        }
        */

        $thumbnail_img = NULL;

        // Check the filter, if the image is determined form the feed or forcefully using the featured image
        $force_source_default_thumbnail = apply_filters( 'wprss_et_image_priority', FALSE );
        // Using the default thumbnail, if the filter returned TRUE, or an array with the source id in it
        $using_default_thumbnail =
            ( $force_source_default_thumbnail === TRUE ) ||
            ( is_array( $force_source_default_thumbnail ) && in_array( $post_ID, $force_source_default_thumbnail ) );

        $featured_image = wp_get_attachment_url( get_post_thumbnail_id( $post_ID ) );

        // If using the featured image, and it has been set, use the featured image
        if ( $using_default_thumbnail && !empty( $featured_image ) ) {
            $thumbnail_img = $featured_image;
        }
        else {
            // Get the thumbnail_to_use option (thumbnail selector since 1.8)
            $thumbnail_to_use = isset( $thumbnails_settings['thumbnail_to_use'] )? $thumbnails_settings['thumbnail_to_use'] : 'auto';

            // check the settings
            switch( $thumbnail_to_use ) {
                default:
                // AUTO DETECT
                case 'auto':
                    $thumbnail_img = wprss_et_auto_detect_thumbnail( $item );
                    break;
                // FIRST IMAGE IN EXCERPT
                case 'first':
                    $thumbnail_img = wprss_et_first_image( $item );
                    break;
                // MEDIA THUMBNAIL TAG
                case 'media:thumbnail':
                    $thumbnail_img = wprss_et_get_media_thumbnail_image( $item );
                    break;
                // ENCLOSURE
                case 'enclosure':
                    $thumbnail_img = wprss_et_get_enclosure_image( $item );
                    break;
            }

            // If no image was found, and we can use the default thumbnail
            if ( ( is_null( $thumbnail_img ) || $thumbnail_img == '' ) && $can_use_def_thumbnail ) {
                wprss_log( 'No image determed! Beginning fallback image processing...', NULL, WPRSS_LOG_LEVEL_SYSTEM );
                // If the featured image for the feed source has been set, use it
                if ( !empty( $featured_image ) ) {
                    $thumbnail_img = $featured_image;
                    wprss_log( 'Falling back to feed source\'s default thumbnail', NULL, WPRSS_LOG_LEVEL_SYSTEM );
                }
                // Otherwise get it from the feed
                else {
                    wprss_log( 'Attempting to get the rss feed\'s own fallback image ...', NULL, WPRSS_LOG_LEVEL_SYSTEM );
                    // We have to get fetch the feed again to check for a main image, this probably needs to be improved
                    // although speed tests didn't show any improvement in execution time with this piece commented out
                    $source_url = get_post_meta( $post_ID, 'wprss_url', true );
                    $feed = wprss_fetch_feed( $source_url );
                    if ( ! $feed->error() ) {
                        // this gets the feed's main image, if it exists
                        if ( !is_null( $feed->get_image_url() ) && @getimagesize( $feed->get_image_url() ) ) {
                            $thumbnail_img = $feed->get_image_url();
                            wprss_log_obj( 'Got', $thumbnail_img, NULL, WPRSS_LOG_LEVEL_SYSTEM );
                        } else {
                            wprss_log_obj( 'Failed to fetch the rss feed\'s fallback image.', $thumbnail_img, NULL, WPRSS_LOG_LEVEL_SYSTEM );
                        }
                    } else {
                        wprss_log( 'Failed to fetch the rss feed to determine the rss feed fallback image.', $thumbnail_img, NULL, WPRSS_LOG_LEVEL_SYSTEM );
                    }
                }
            }

            // SINCE 1.8
            // ---------
            // If no image was determined as the thumbnail, saved NULL in meta.
            // Let the template handle the default images, rather than saving it in meta, to
            // allow users to change the default image, and have all items with no image recieve
            // the new default image.
        }

        update_post_meta( $inserted_ID, 'wprss_item_thumbnail', $thumbnail_img );
        wprss_log( 'Updated feed item "wprss_item_thumbnail" meta field with thumbnail', $thumbnail_img, NULL, WPRSS_LOG_LEVEL_SYSTEM );
    }


    add_action( 'wprss_admin_scripts_styles', 'wprss_et_add_media_uploader_scripts' );
    /**
     * Add media uploader scripts
     * @since 1.0
     */
    function wprss_et_add_media_uploader_scripts() {
        wp_enqueue_media(); // This function loads in the required media files for the media manager.
        wp_enqueue_script( 'admin-media-uploader', WPRSS_ET_JS . 'admin-media-uploader.js', array( 'jquery' ) );
        wp_enqueue_script( 'admin-et-settings', WPRSS_ET_JS . 'admin-et-settings.js', array( 'jquery' ) );
    }

    add_action( 'wp_enqueue_scripts', 'wprss_et_enqueue_scripts' );
    /**
     * Enqueues front end scripts and styles
     */
    function wprss_et_enqueue_scripts() {
        wp_enqueue_style( 'wprss-et-styles', WPRSS_ET_CSS . 'styles.css' );
    }


    add_action( 'wprss_fields_export', 'wprss_et_fields_export' );
    /**
     * Add fields for exporting function in core plugin
     *
     * @since 1.0
     */
    function wprss_et_fields_export( $args ) {
        $args['wprss_settings_excerpts']   = get_option( 'wprss_settings_excerpts' );
        $args['wprss_settings_thumbnails'] = get_option( 'wprss_settings_thumbnails' );
        return $args;
    }


    add_filter( 'default_args', 'wprss_et_shortcode_add_args' );
    /**
     * Sets the default arguments for excerpt and thumbnail fields.
     *
     * @since 1.0
     * @return array The default arguments for excerpt and thumbnail arguments.
     */
    function wprss_et_shortcode_add_args() {
        $args = array(
            'thumbnails' => NULL,
            'excerpts' => NULL
        );
        return $args;
    }


    /**
     * Retrieves the query string from the given URL.
     *
     * @param url The URL from which to extract the query string.
     * @return string The extracted query string. Returns an empty string if no query string is found.
     * @since 1.1
     */
    function wprss_get_query_string( $url ) {
    	$query = '';
        // Get the index of the '?' character in the url
        $query_pos = strpos( $url, '?' );
        // If the index returned is not false, get the substring after the '?' character
        if ( $query_pos !== FALSE )
            $query = substr( $url, $query_pos );
        return $query;
    }


    /**
     * Checks if a remote file exists, by pinging it and checking the status code.
     *
     * @param $url The url of the remote resource
     * @since 1.3
     */
    function wprss_et_remote_file_exists( $url ) {
        $exists = FALSE;

        $curl = curl_init($url);
        // ping the page
        curl_setopt( $curl, CURLOPT_NOBODY, true );
        $response = curl_exec($curl);
        // if the response is not FALSE
        if ( $response !== FALSE ) {
            // check the response status code
            $statusCode = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
            // If recieved a status code of OK ( 200 )
            if ( $statusCode == 200 ) {
                $exists = TRUE;
            }
        }
        // Close the curl instance
        curl_close( $curl );

        return $exists;
    }


    add_filter( 'wprss_single_feed_output', 'wprss_et_add_social_buttons', 10, 2 );
    /**
     * Adds social buttons to feed items rendered through the shortcode.
     *
     * @param $content Feed item content
     * @since 1.6.3
     */
    function wprss_et_add_social_buttons( $content, $permalink ) {
        $wprss_settings_excerpts = get_option( 'wprss_settings_excerpts' );
        $social_buttons_enabled = ( isset( $wprss_settings_excerpts['social_buttons'] ) ? $wprss_settings_excerpts['social_buttons'] : FALSE );

        if ( $social_buttons_enabled == 1 ) {
            $twitterVia = isset($wprss_settings_excerpts['twitter_via']) ? $wprss_settings_excerpts['twitter_via'] : 'wprssaggregator';
            $content .= '<div class="wprss-et-social-buttons"><div class="fb-like" data-href="' . $permalink . '" data-layout="standard" data-action="like" data-show-faces="true" data-share="true"></div>
<a href="https://twitter.com/share" class="twitter-share-button" data-url="' . $permalink . '" data-via="'.$twitterVia.'">Tweet</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?"http":"https";if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document, "script", "twitter-wjs");</script>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>
<g:plusone href="' . $permalink . '"></g:plusone></div>';
        }

        return $content;
    }


    add_filter( 'feed_output', 'wprss_et_register_social_buttons', 100000, 1 );
    /**
     * Echoes async scripts before any social buttons.
     *
     * @param $content Page content
     * @since 1.6.3
     */
    function wprss_et_register_social_buttons( $content ) {
        return '<div id="fb-root"></div>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1&appId=629802223740065";
  fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "facebook-jssdk"));</script>' . $content;
    }


	/**
	 * Retrieves the cache TTL.
	 *
	 * This value defaults to the value of the WPRSS_ET_IMAGE_CACHE_TTL constant.
	 * It can be modified by implementing a handler for the `wprss_et_image_cache_ttl` filter.
	 *
	 * @since 1.9.2
	 * @return int The number of seconds representing the cache time to live.
	 */
	function wprss_et_get_image_cache_ttl() {
		return apply_filters( 'wprss_et_image_cache_ttl', WPRSS_ET_IMAGE_CACHE_TTL );
	}


	/**
	 * Retrieves the cache controller.
	 *
	 * This value can be modified by implementing a handler for the `wprss_et_image_cache` filter.
	 *
	 * @since 1.9.2
	 * @see wprss_et_get_image_cache_ttl()
	 * @return \Aventura\Wprss\Core\Caching\ImageCache The instance of the cache controller.
	 */
	function wprss_et_get_image_cache() {
        static $cache = null;

        if (is_null($cache)) {
            $cache = new Caching\ImageCache();
            $cache->set_ttl(wprss_et_get_image_cache_ttl());
        }

		return apply_filters( 'wprss_et_image_cache', $cache );
	}


	/**
	 * Retrieves an image identified by it's URL from cache.
	 *
	 * If cache doesn't exist, or is expired, image will be downloaded first.
	 * This value can be overridden by implementing a handler for the `wprss_et_downloaded_image` filter.
	 *
	 * @since 1.9.2
	 * @see wprss_et_get_image_cache()
	 * @param string $url The URL of the image to download
	 * @return \Aventura\Wprss\Core\Caching\ImageCache\Image The instance of the retrieved image
	 */
	function wprss_et_download_image( $url ) {
        if ( empty($url) )
            return apply_filters( 'wprss_et_downloaded_image', new WP_Error( 'wprss_et_download_image_failed', __( 'Image URL cannot be empty' ) ) );

		try {
			$image = wprss_et_get_image_cache()->get_images( $url );
		} catch ( Exception $e ) {
			$message = $e->getMessage();
			$image = new WP_Error( 'wprss_et_download_image_failed', $message, $url );
			wprss_log( sprintf( 'Image could not be downloaded from "%1$s": %2$s', $url, $message ), __FUNCTION__, WPRSS_LOG_LEVEL_SYSTEM );
		}

		return apply_filters( 'wprss_et_downloaded_image', $image );
	}
