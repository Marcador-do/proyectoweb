<?php    

    add_action( 'wprss_admin_init', 'wprss_et_add_settings' );
    /**
     * Adds some more settings fields pertaining to thumbnails and exerpts
     * @since 1.0
     */    
    function wprss_et_add_settings() {

        register_setting( 
            'wprss_settings_excerpts',                       // A settings group name.
            'wprss_settings_excerpts',                       // The name of an option to sanitize and save.
            'wprss_et_settings_excerpts_validate'            // A callback function that sanitizes the option's value.
        );                 

        register_setting( 
            'wprss_settings_thumbnails',                       
            'wprss_settings_thumbnails',                        
            'wprss_et_settings_thumbnails_validate' 
        );                           

        add_settings_section(   
            'wprss_settings_excerpts_section',               // ID used to identify this section and with which to register options      
            __( 'Excerpt Settings', WPRSS_TEXT_DOMAIN ),               // Title to be displayed on the administration page  
            'wprss_et_settings_excerpts_callback',           // Callback used to render the description of the section
            'wprss_settings_excerpts'                        // Page on which to add this section of options  
        ); 

        add_settings_section(   
            'wprss_settings_thumbnails_section',                          
            __( 'Thumbnail Settings', WPRSS_TEXT_DOMAIN ),    
            'wprss_et_settings_thumbnails_callback',               
            'wprss_settings_thumbnails'                         
        );    

        add_settings_field( 
            'wprss-settings-excerpts-enable',                // ID used to identify the field throughout the theme
            __( 'Enable excerpts', WPRSS_TEXT_DOMAIN ),                // The label to the left of the option interface element  
            'wprss_et_setting_excerpts_enable_callback',     // The name of the function responsible for rendering the option interface
            'wprss_settings_excerpts',                       // The page on which this option will be displayed  
            'wprss_settings_excerpts_section'                // The name of the section to which this field belongs  
        );         

        add_settings_field( 
            'wprss-settings-excerpts-word-limit', 
            __( 'Excerpts word limit', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_excerpts_word_limit_callback', 
            'wprss_settings_excerpts', 
            'wprss_settings_excerpts_section' 
        ); 

        add_settings_field( 
            'wprss-settings-excerpts-ending', 
            __( 'Excerpts ending', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_excerpts_ending_callback', 
            'wprss_settings_excerpts', 
            'wprss_settings_excerpts_section' 
        );        

/*        add_settings_field( 
            'wprss-settings-excerpts-strip-html-tags', 
            __( 'HTML tags to strip', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_excerpts_strip_html_tags_callback', 
            'wprss_settings_excerpts', 
            'wprss_settings_excerpts_section' 
        ); 
*/
        add_settings_field( 
            'wprss-settings-morelink-enable', 
            __( 'Enable "Read more" link', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_morelink_enable_callback', 
            'wprss_settings_excerpts', 
            'wprss_settings_excerpts_section' 
        );         

        add_settings_field( 
            'wprss-settings-excerpts-read-more', 
            __( 'Read more text', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_excerpts_read_more_callback', 
            'wprss_settings_excerpts', 
            'wprss_settings_excerpts_section' 
        ); 

        add_settings_field( 
            'wprss-settings-thumbnails-enable', 
            __( 'Enable thumbnails', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_thumbnails_enable_callback', 
            'wprss_settings_thumbnails', 
            'wprss_settings_thumbnails_section' 
        ); 

        add_settings_field( 
            'wprss-settings-default-thumbnail', 
            __( 'Default thumbnail image', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_default_thumbnail_callback', 
            'wprss_settings_thumbnails',
            'wprss_settings_thumbnails_section' 
        );          

        add_settings_field( 
            'wprss-settings-thumbnails-width', 
            __( 'Thumbnail image width', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_thumbnails_width_callback', 
            'wprss_settings_thumbnails', 
            'wprss_settings_thumbnails_section' 
        );  

        add_settings_field( 
            'wprss-settings-thumbnails-height', 
            __( 'Thumbnail image height', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_thumbnails_height_callback', 
            'wprss_settings_thumbnails', 
            'wprss_settings_thumbnails_section' 
        );  

        add_settings_field( 
            'wprss-settings-link-thumbnail', 
            __( 'Link thumbnail to permalink', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_link_thumbnail_callback', 
            'wprss_settings_thumbnails',
            'wprss_settings_thumbnails_section' 
        );

        add_settings_field( 
            'wprss-settings-show-default-thumbnail', 
            __( 'When feed item has no thumbnail', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_use_def_thumbnail_callback', 
            'wprss_settings_thumbnails',
            'wprss_settings_thumbnails_section' 
        );

        add_settings_field( 
            'wprss-settings-thumbnail-chooser', 
            __( 'Image to use as thumbnail', WPRSS_TEXT_DOMAIN ), 
            'wprss_et_setting_thumbnail_selector_callback', 
            'wprss_settings_thumbnails',
            'wprss_settings_thumbnails_section' 
        );

        add_settings_field(
            'wprss-settings-social-buttons',
            __( 'Enable social buttons', WPRSS_TEXT_DOMAIN ),
            'wprss_et_setting_social_buttons_callback',
            'wprss_settings_excerpts',
            'wprss_settings_excerpts_section'
        );
        
        add_settings_field(
            'wprss-settings-social-twitter-via',
            __( 'Twitter: Share via user', WPRSS_TEXT_DOMAIN ),
            'wprss_et_setting_social_twitter_via_callback',
            'wprss_settings_excerpts',
            'wprss_settings_excerpts_section'
        );

        $options = get_option( 'wprss_settings_thumbnails' ); 
        if ( ! empty( $options['default_thumbnail'] ) ) {       
            add_settings_field( 
                'wprss-settings-default-thumbnail-preview', 
                __( 'Default thumbnail image preview', WPRSS_TEXT_DOMAIN ), 
                'wprss_et_setting_default_thumbnail_preview_callback', 
                'wprss_settings_thumbnails',
                'wprss_settings_thumbnails_section' 
            ); 
        }

		if ( version_compare(WPRSS_VERSION, '4.5', '<') ) {
        	add_settings_section(
				'wprss_settings_et_licenses_section',
				__( 'Excerpts & Thumbnails License', WPRSS_TEXT_DOMAIN ),
				'wprss_et_settings_license_callback',
				'wprss_settings_license_keys'
			);

			add_settings_field(
				'wprss-settings-license',
				__( 'License Key', WPRSS_TEXT_DOMAIN ),
				'wprss_et_setting_license_callback',
				'wprss_settings_license_keys',
				'wprss_settings_et_licenses_section'
			);

			add_settings_field(
				'wprss-settings-license-activation',
				__( 'Activate License', WPRSS_TEXT_DOMAIN ),
				'wprss_et_setting_license_activation_callback',
				'wprss_settings_license_keys',
				'wprss_settings_et_licenses_section'
			);
		}

    }    


    /** 
     * Draw the excerpt settings section header
     * @since 1.0
     */
    function wprss_et_settings_excerpts_callback() {
        echo '<p>' . 
            sprintf(
                __( 'Settings for feed item excerpts, via the <a href="%s">Excerpts & Thumbnails</a> add-on.', WPRSS_TEXT_DOMAIN ),
                esc_attr("http://www.wprssaggregator.com/extension/excerpts-thumbnails/")
            ) . '</p>';
    }  


    /** 
     * Draw the thumbnail settings section header
     * @since 1.0
     */
    function wprss_et_settings_thumbnails_callback() {
        echo '<p>' . 
            sprintf(
                __( 'Settings for thumbnails, via the <a href="%s">Excerpts & Thumbnails</a> add-on.', WPRSS_TEXT_DOMAIN ),
                esc_attr("http://www.wprssaggregator.com/extension/excerpts-thumbnails/")
            ) . '</p>';
    }  


    /** 
     * Enable or disable excerpts
     * @since 1.0
     */
    function wprss_et_setting_excerpts_enable_callback( $args ) {
        $options = get_option( 'wprss_settings_excerpts' );                    
        echo "<input id='excerpts-enable' name='wprss_settings_excerpts[excerpts_enable]' type='checkbox' value='1' " . checked( 1, $options['excerpts_enable'], false ) . " />";   
        echo "<label for='excerpts-enable'>". __( 'Check this box to enable excerpt functionality', WPRSS_TEXT_DOMAIN ) . "</label>";   
    }


    /** 
     * Set excerpts word limit
     * @since 1.0
     */
    function wprss_et_setting_excerpts_word_limit_callback( $args ) {
        $options = get_option( 'wprss_settings_excerpts' );                    
        echo "<input id='excerpts-word-limit' name='wprss_settings_excerpts[excerpts_word_limit]' type='text' value='{$options['excerpts_word_limit']}' class='small-text' />";   
        echo "<label for='excerpts-word-limit'>" . __( 'The number of words used in the excerpt displayed', WPRSS_TEXT_DOMAIN ) . "</label>";  
    }


    /** 
     * Ending of excerpt
     * @since 1.0
     */
    function wprss_et_setting_excerpts_ending_callback( $args ) {
        $options = get_option( 'wprss_settings_excerpts' );                    
        echo "<input id='excerpts-ending' name='wprss_settings_excerpts[excerpts_ending]' type='text' value='{$options['excerpts_ending']}' class='small-text' />";   
        echo "<label for='excerpts-ending'>" . __( 'Characters appearing at end of excerpt', WPRSS_TEXT_DOMAIN ) . "</label>";  
    }


    /** 
     * Enable or disable 'read more' link
     * @since 1.0
     */
    function wprss_et_setting_morelink_enable_callback( $args ) {
        $options = get_option( 'wprss_settings_excerpts' );                    
        echo "<input id='morelink-enable' name='wprss_settings_excerpts[morelink_enable]' type='checkbox' value='1' " . checked( 1, $options['morelink_enable'], false ) . " />";   
        echo "<label for='morelink-enable'>" . __( "Check this box to enable 'Read more' link functionality", WPRSS_TEXT_DOMAIN ) . "</label>";   
    }


    /** 
     * Set 'read more' link text 
     * @since 1.0
     */
    function wprss_et_setting_excerpts_read_more_callback( $args ) {
        $options = get_option( 'wprss_settings_excerpts' );                    
        echo "<input id='excerpts-read-more' name='wprss_settings_excerpts[excerpts_read_more]' type='text' value='{$options['excerpts_read_more']}' />";   
    }


    /** 
     * Set HTML tags to be stripped from feed content
     * @since 1.0
     */
    function wprss_et_setting_excerpts_strip_html_tags_callback( $args ) {
        $options = get_option( 'wprss_settings_excerpts' );                    
        echo "<textarea id='strip-html-tags' name='wprss_settings_excerpts[strip_html_tags]' value='{$options['strip_html_tags']}'></textarea>";   
    }


    /** 
     * Enable or disable thumbnail functionality
     * @since 1.0
     */
    function wprss_et_setting_thumbnails_enable_callback( $args ) {
        $options = get_option( 'wprss_settings_thumbnails' );                    
        echo "<input id='thumbnails-enable' name='wprss_settings_thumbnails[thumbnails_enable]' type='checkbox' value='1' " . checked( 1, $options['thumbnails_enable'], false ) . " />";   
        echo "<label for='thumbnails-enable'>" . __( 'Check this box to enable thumbnail functionality', WPRSS_TEXT_DOMAIN ) . "</label>";  
    }


    /**
     * Sets the option to either use the default thumbnail, or show no thumbnail when the
     * feed item has no thumbnail.
     * @since 1.3
     */
    function wprss_et_setting_use_def_thumbnail_callback( $args ) {
        $thumb_settings = get_option( 'wprss_settings_thumbnails' );
        $use = ( isset( $thumb_settings['use_def_thumbnail'] ) )?  $thumb_settings['use_def_thumbnail'] : 'true';
        $options = array(
            'true'  => __('Use the default thumbnail', WPRSS_TEXT_DOMAIN),
            'false' => __('Show no thumbnail', WPRSS_TEXT_DOMAIN)
        );
        echo "<select id='use-def-thumbnail' name='wprss_settings_thumbnails[use_def_thumbnail]'>";
        foreach( $options as $key => $value ) {
            $selected = ( $key === $use )? ' selected' : '';
            echo "  <option value='$key'$selected>" . $value . "</option>";
        }
        echo "</select>";
    }


    /**
     * Prints the thumbnail selector - the dropdown that allows users to choose with image to use as the thumbnail
     * @since 1.3
     */
    function wprss_et_setting_thumbnail_selector_callback( $args ) {
        $thumb_settings = get_option( 'wprss_settings_thumbnails' );
        $use = ( isset( $thumb_settings['thumbnail_to_use'] ) )?  $thumb_settings['thumbnail_to_use'] : 'auto';
        $options = array(
            'auto'              =>  __('Auto Detect', WPRSS_TEXT_DOMAIN),
            'first'             =>  __('First image in excerpt', WPRSS_TEXT_DOMAIN),
            'media:thumbnail'   =>  __('Image in &lt;media:thumbnail&gt; tag', WPRSS_TEXT_DOMAIN),
            'enclosure'         =>  __('Image in &lt;enclosure&gt; tag', WPRSS_TEXT_DOMAIN)
        );

        ?>
        <select id='thumbnail-selector' name='wprss_settings_thumbnails[thumbnail_to_use]'>

        <?php
            foreach( $options as $key => $value ) {
                $selected = ( $key === $use )? ' selected' : '';
                echo "  <option value='$key'$selected>" . $value . "</option>";
            }
        ?>

        </select>
        <br />
        <label class="description" for="thumbnail-selector">
            <?php _e( 'Choose the image to use as the thumbnail.', WPRSS_TEXT_DOMAIN ); ?>
            <?php _e( 'Use Auto Detect to use the method used in older versions of Excerpts &amp; Thumbnails.', WPRSS_TEXT_DOMAIN ); ?>
        </label>

        <?php
    }



    /**
     * Sets the thumbnails to link to the permalink location.
     * @since 1.3
     */
    function wprss_et_setting_link_thumbnail_callback( $args ) {
        $thumb_settings = get_option( 'wprss_settings_thumbnails' );
        $link = ( isset( $thumb_settings['link_thumbnail'] ) )?  $thumb_settings['link_thumbnail'] : 'false';
        $checked = ( $link === 'true' )? ' checked' : '';
        echo "<input id='link-thumbnail' name='wprss_settings_thumbnails[link_thumbnail]' value='true' type='checkbox' $checked>";
        echo "<label for='link-thumbnail'>" . __( 'Check this box to link the thumbnail to the feed item\'s permalink', WPRSS_TEXT_DOMAIN ) . "</label>"; 
    }


    /** 
     * Set default thumbnail image
     * @since 1.0
     */
    function wprss_et_setting_default_thumbnail_callback( $args ) {
        $options = get_option( 'wprss_settings_thumbnails' );                           
        echo "<input id='default-thumbnail' name='wprss_settings_thumbnails[default_thumbnail]' type='text' value='{$options['default_thumbnail']}' />";   
        echo "<input id='default-thumbnail-button' type='button' class='button' value='Choose image' />";   
    }    


    /** 
     * Set default thumbnails image width
     * @since 1.0
     */
    function wprss_et_setting_thumbnails_width_callback( $args ) {
        $options = get_option( 'wprss_settings_thumbnails' );                           
        echo "<input id='thumbnails-width' name='wprss_settings_thumbnails[thumbnail_width]' type='text' value='{$options['thumbnail_width']}' class='small-text'/><small>px</small>";   
        echo "<label for='thumbnails-width'>" . __( 'The thumbnail width in pixels', WPRSS_TEXT_DOMAIN ) . "</label>";   
    }


    /** 
     * Set default thumbnail image height
     * @since 1.0
     */
    function wprss_et_setting_thumbnails_height_callback( $args ) {
        $options = get_option( 'wprss_settings_thumbnails' );                    
        echo "<input id='thumbnails-height' name='wprss_settings_thumbnails[thumbnail_height]' type='text' value='{$options['thumbnail_height']}' class='small-text' /><small>px</small>";   
        echo "<label for='thumbnails-height'>" . __( 'The thumbnail height in pixels', WPRSS_TEXT_DOMAIN ) . "</label>";   
    }


    /** 
     * Default thumbnail image preview
     * http://wp.tutsplus.com/tutorials/creative-coding/how-to-integrate-the-wordpress-media-uploader-in-theme-and-plugin-options/
     * @since 1.0
     */
    function wprss_et_setting_default_thumbnail_preview_callback( $args ) {
        $options = get_option( 'wprss_settings_thumbnails' ); ?>
        <div id="default-thumbnail-preview" style="min-height: 100px;">
            <?php 
            if ( function_exists( 'wpthumb' ) ) {
                echo '<img style="max-width:100%;" src="' . wpthumb( $options['default_thumbnail'], 'width=' . $options['thumbnail_width'] . 
                    '&height=' . $options['thumbnail_height'] . '&crop=1' ) . '" />';
            } 
            else {
                echo __( 'WPThumb library not found', WPRSS_TEXT_DOMAIN ); 
            }
            ?>            
        </div>
        <?php
    }

    /** 
     * Draw the licenses settings section header
     * @since 1.1
     */
    function wprss_et_settings_license_callback() {
        //  echo '<p>' . ( 'License details' ) . '</p>';
    }     


    /** 
     * Set license
     * @since 1.1
     */
    function wprss_et_setting_license_callback( $args ) {
        $license_keys = get_option( 'wprss_settings_license_keys' ); 
        $et_license_key = ( isset( $license_keys['et_license_key'] ) ) ? $license_keys['et_license_key'] : FALSE;      
        echo "<input id='wprss-et-license-key' name='wprss_settings_license_keys[et_license_key]' type='text' value='" . esc_attr( $et_license_key ) ."' />";
        echo "<label class='description' for='wprss-et-license-key'>" . __( 'Enter your license key', WPRSS_TEXT_DOMAIN ) . '</label>';                   
    }    


    /** 
     * License activation button and indicator
     * @since 1.1
     */
    function wprss_et_setting_license_activation_callback( $args ) {
        $license_keys = get_option( 'wprss_settings_license_keys' ); 
        $license_statuses = get_option( 'wprss_settings_license_statuses' ); 
        $et_license_key = ( isset( $license_keys['et_license_key'] ) ) ? $license_keys['et_license_key'] : FALSE;
        $et_license_status = ( isset( $license_statuses['et_license_status'] ) ) ? $license_statuses['et_license_status'] : FALSE;
    

        if( $et_license_status != FALSE && $et_license_status == 'valid' ) { ?>
            <span style="color:green;"><?php _e( 'active', WPRSS_TEXT_DOMAIN ); ?></span>
            <?php wp_nonce_field( 'wprss_et_license_nonce', 'wprss_et_license_nonce' ); ?>
            <input type="submit" class="button-secondary" name="wprss_et_license_deactivate" value="<?php _e( 'Deactivate License', WPRSS_TEXT_DOMAIN ); ?>"/>
        <?php } 
        else {
            wp_nonce_field( 'wprss_et_license_nonce', 'wprss_et_license_nonce' ); ?>
            <input type="submit" class="button-secondary" name="wprss_et_license_activate" value="<?php _e( 'Activate License', WPRSS_TEXT_DOMAIN ); ?>"/>

        <?php }
    }

    /**
     * Option for social buttons
     * @since 
     */
    function wprss_et_setting_social_buttons_callback( $args ) {
        $excerpts_settings = get_option( 'wprss_settings_excerpts' );
        $social_buttons_enabled = ( isset( $excerpts_settings['social_buttons'] ) ? $excerpts_settings['social_buttons'] : FALSE ); ?>

        <input id="social_buttons_switch" type="checkbox" name="wprss_settings_excerpts[social_buttons]" value="1" <?php checked( 1, $social_buttons_enabled, TRUE ); ?> />
        <label for="social_buttons_switch"><?php _e('Check this box to add social buttons to every post created', WPRSS_TEXT_DOMAIN); ?></label>
        <?php
    }

    /**
     * Option for Twitter's 'via' data attribute
     * @since 
     */
    function wprss_et_setting_social_twitter_via_callback( $args ) {
        $excerpts_settings = get_option( 'wprss_settings_excerpts' );
        $fieldName = 'twitter_via';
        $fieldId = str_replace('-', '_', 'wprss-settings-social-twitter-via');
        $fieldValue = ( isset( $excerpts_settings[$fieldName] ) ? $excerpts_settings[$fieldName] : 'wprssaggregator' ); ?>

        <input id="<?php echo $fieldId ?>" type="text" name="wprss_settings_excerpts[<?php echo $fieldName ?>]" value="<?php echo $fieldValue ?>" />
        <label for="<?php echo $fieldId ?>"><?php _e('This will be also appended to the tweet text', WPRSS_TEXT_DOMAIN) ?></label>
        <?php
    }


    /** 
     * Validate inputs from the excerpts settings page
     * @since 1.0
     */
    function wprss_et_settings_excerpts_validate( $input ) {
        // Create our array for storing the validated options
        $output = array();
        
        // Loop through each of the incoming options
        foreach( $input as $key => $value ) {
            
            // Check to see if the current option has a value. If so, process it.
            if( isset( $input[ $key ] ) ) {
            
                // Strip all HTML and PHP tags and properly handle quoted strings
                $output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );
                
            } // end if
            
        } // end foreach

        if ( ! isset( $input['excerpts_enable'] ) || $input['excerpts_enable'] != '1' )
            $output['excerpts_enable'] = 0;
        else
            $output['excerpts_enable'] = 1;        

        if ( ! isset( $input['morelink_enable'] ) || $input['morelink_enable'] != '1' )
            $output['morelink_enable'] = 0;
        else
            $output['morelink_enable'] = 1;                     
        
        // Return the array processing any additional functions filtered by this action
        return apply_filters( 'wprss_settings_excerpts_validate', $output, $input );
    }


    /** 
     * Validate inputs from the thumbnails settings page
     * @since 1.0
     */
    function wprss_et_settings_thumbnails_validate( $input ) {
        // Create our array for storing the validated options
        $output = array();
        
        // Loop through each of the incoming options
        foreach( $input as $key => $value ) {
            
            // Check to see if the current option has a value. If so, process it.
            if( isset( $input[ $key ] ) ) {
            
                // Strip all HTML and PHP tags and properly handle quoted strings
                $output[ $key ] = strip_tags( stripslashes( $input[ $key ] ) );
                
            } // end if   
            
        } // end foreach

        if ( ! isset( $input['thumbnails_enable'] ) || $input['thumbnails_enable'] != '1' )
            $output['thumbnails_enable'] = 0;
        else
            $output['thumbnails_enable'] = 1;        
        
        // Return the array processing any additional functions filtered by this action
        return apply_filters( 'wprss_settings_thumbnails_validate', $output, $input );
    }


    add_action( 'wprss_add_settings_fields_sections', 'wprss_et_add_settings_fields_sections', 10, 1 );
    /** 
     * Add settings fields and sections for Excerpts and Thumbnails
     * @since 1.0
     */
    function wprss_et_add_settings_fields_sections( $active_tab ) {
            
        if( $active_tab == 'excerpts_settings' ) {         
            settings_fields( 'wprss_settings_excerpts' );
            do_settings_sections( 'wprss_settings_excerpts' ); 
        }

        if( $active_tab == 'thumbnails_settings' ) {         
            settings_fields( 'wprss_settings_thumbnails' );
            do_settings_sections( 'wprss_settings_thumbnails' ); 
        }          
    }

     
   /* function wprss_et_sanitize_license( $new ) {
        $old = get_option( 'wprss_et_license_key' );
        if( $old && $old != $new ) {
            delete_option( 'wprss_et_license_status' ); // new license has been entered, so must reactivate
        }
        return $new;
    }*/


    add_action( 'wprss_options_tabs', 'wprss_et_add_settings_tabs' );
    /** 
     * Add settings tabs for Excerpts and Thubmanils on the Settings page
     * @since 1.0
     */
    function wprss_et_add_settings_tabs( $args ) {
        $args['excerpts'] = array(
                        'label' => __( 'Excerpts', WPRSS_TEXT_DOMAIN ),
                        'slug' => 'excerpts_settings'
        );
        $args['thumbnails'] = array(
                        'label' => __( 'Thumbnails', WPRSS_TEXT_DOMAIN ),
                        'slug' => 'thumbnails_settings'
        );     
        return $args;
    }
