<?php

/**
 * This file contains functions that extend the core's custom RSS feed.
 *
 * @since 1.6
 */


add_action( 'wprss_custom_feed_entry', 'wprss_et_extend_custom_feed' );
/**
 * Extends the custom feed for each entry to include the thumbnails.
 * 
 * @since 1.6 
 */
function wprss_et_extend_custom_feed( $feed_item_id ) {
	// Get the thumbnail settings
	$thumbnails_settings = get_option( 'wprss_settings_thumbnails' );

	// Check if thumbnails are enabled
	$thumbnails_enabled = $thumbnails_settings['thumbnails_enable'];
	$thumbnails_enabled =  strcasecmp( $thumbnails_enabled, 'true' ) == 0
						|| strcasecmp( $thumbnails_enabled, '1' ) == 0
						|| strcasecmp( $thumbnails_enabled, 'on' ) == 0
						|| strcasecmp( $thumbnails_enabled, 'yes' ) == 0;

	// If Thumbnails enabled
	if ( $thumbnails_enabled === TRUE ) {
		
		// Get the thumbnail for the feed item
		$thumbnail_img = get_post_meta( $feed_item_id, 'wprss_item_thumbnail', true );

		// IF feed item has no thumbnail
        if ( empty( $thumbnail_img ) ) {
        	// If a default is set, use the default image
        	// Otherwise, use the image that comes with the plugin
			if ( !empty( $thumbnails_settings['default_thumbnail'] ) ) {
				$thumbnail_img = $thumbnails_settings['default_thumbnail'];
			} else {
				$thumbnail_img = WPRSS_ET_IMG . 'default-thumbnail.png';
			}
		}

		$width = $thumbnails_settings['thumbnail_width'];
		$height = $thumbnails_settings['thumbnail_height'];

		echo "<media:thumbnail url=\"$thumbnail_img\" width=\"$width\" height=\"$height\" />";
	}

	// END OF FUNCTION
}