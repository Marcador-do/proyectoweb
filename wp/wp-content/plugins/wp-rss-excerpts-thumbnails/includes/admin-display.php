<?php

	/**
	 * Handles display-related functionality for admins
	 *
	 * @since 1.1
	 */


	add_filter( 'wprss_set_feed_item_custom_columns', 'wprss_et_add_column' );
	/**
	 * Adds a new 'Thumbnail' column to the 'All Feed Sources' table.
	 * 
	 * @since 1.0
	 * @return array The columns array with an extra 'thumbnail' column
	 */
	function wprss_et_add_column( $columns ) {
		$columns = array( 'cb' => $columns['cb'] ) + array( 'thumbnail' => __( 'Thumbnail', WPRSS_TEXT_DOMAIN ) ) + array_slice( $columns, 1 );
		return $columns;
	}


	add_action( "manage_wprss_feed_item_posts_custom_column", "wprss_et_show_tumbnail_column", 10, 2 );
	/**
	 * Prints out the thumbnail for each feed source in the 'All 
	 * Feed Sources' table.
	 * 
	 * @since 1.0
	 */
	function wprss_et_show_tumbnail_column( $column, $post_id ) {
		if ( $column === 'thumbnail' ) {
			$thumbnails_settings = get_option( 'wprss_settings_thumbnails' );
			$thumbnail_img = get_post_meta( $post_id, 'wprss_item_thumbnail', true );

			if ( empty( $thumbnail_img ) ) {
                if ( !empty( $thumbnails_settings['default_thumbnail'] ) ) {                       
                    $thumbnail_img = $thumbnails_settings['default_thumbnail'];
                }
            }
            
            $query_str = wprss_get_query_string( $thumbnail_img );
			$thumbnail = wpthumb( $thumbnail_img, array(
					'width' 	=>	'90',
					'height'	=>	'90',
					'crop'		=>	'1',
					'background_fill'	=>	'solid'
				)
			) . $query_str;
			echo '<img src="' . esc_attr( $thumbnail ) . '" height="90" width="90" />';
		}
	}