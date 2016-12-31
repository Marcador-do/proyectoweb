<?php 


//in admin-metaboxes.php

    /**     
     * Generate the Save Feed Source meta box
     * 
     * @since 2.0
     * @deprecated
     */  
  /*  function wprss_save_feed_source_meta_box_callback() {
        global $post;
        
        // insert nonce??

        echo '<input type="submit" name="publish" id="publish" class="button-primary" value="Save" tabindex="5" accesskey="s">';
                
        /**
         * Check if user has disabled trash, in that case he can only delete feed sources permanently,
         * else he can deactivate them. By default, if not modified in wp_config.php, EMPTY_TRASH_DAYS is set to 30.
         */
      /*  if ( current_user_can( "delete_post", $post->ID ) ) {
            if ( ! EMPTY_TRASH_DAYS )
                $delete_text = __( 'Delete Permanently', 'wprss' );
            else
                $delete_text = __( 'Move to Trash', 'wprss' );
                
        echo '&nbsp;&nbsp;<a class="submitdelete deletion" href="' . get_delete_post_link( $post->ID ) . '">' . $delete_text . '</a>';
        }
    }*/

// in admin-metaboxes.php
// in wprss_add_meta_boxes() 

      /*  add_meta_box(
            'wprss-save-link-side-meta',
            'Save Feed Source',
            'wprss_save_feed_source_meta_box',
            'wprss_feed',
            'side',
            'high'
        );
        
        add_meta_box(
            'wprss-save-link-bottom-meta',
            __( 'Save Feed Source', 'wprss' ),
            'wprss_save_feed_source_meta_box',
            'wprss_feed',
            'normal',
            'low'
        );*/




// in wp-rss-excerpts-thumbnails.php

  /*  function admin_post_thumbnail_kittenifier( $content ) {
        // In reality, you might want to replicate some of the code from _wp_post_thumbnail_html(), but this gives you the idea
        $thumbnails_settings = get_option( 'wprss_settings_thumbnails' );
        preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i', $content, $matches );

        if ( isset( $matches ) && !empty( $matches[1][0] ) ) {
            $thumbnail_img = $matches[1][0]; 
        }  
        else $thumbnail_img = '';

        $thumbnail = wpthumb( $thumbnail_img, 'width=' . $thumbnails_settings['thumbnail_width'] . '&height=' . $thumbnails_settings['thumbnail_height'] . '&crop=1' );
        $thumbnail = '<img src="' . $thumbnail . '" />';
        return $thumbnail;

      //  return var_dump($thumbnail_img); //"<img src='http://placekitten.com/200/300' alt='I can has toxoplasma gondii?'/>";           
    }
    add_filter( 'admin_post_thumbnail_html', 'admin_post_thumbnail_kittenifier' );
    */
