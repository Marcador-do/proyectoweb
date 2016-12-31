<?php
    /**
     * Initialize settings to default ones if they are not yet set
     *
     * @since 1.0
     */
    function wprss_et_thumbnails_settings_initialize() {
        // Get the settings from the field in the database, if it exists
        $settings = get_option( 'wprss_settings_thumbnails' );

        if ( FALSE == $settings ) { 
            update_option( 'wprss_settings_thumbnails', wprss_et_get_default_settings_thumbnails() );
        }

        else {
            // Get the default plugin settings.
            $default_settings = wprss_et_get_default_settings_thumbnails();

            // Loop through each of the default plugin settings. 
            foreach ( $default_settings as $setting_key => $setting_value ) {

                // If the setting didn't previously exist, add the default value to the $settings array. 
                if ( ! isset( $settings[ $setting_key ] ) )
                    $settings[ $setting_key ] = $setting_value;
            }

            // Update the plugin settings.
            update_option( 'wprss_settings_thumbnails', $settings );  
        }     
    }    


    /**
     * Returns an array of the default thumbnail settings. Used for plugin activation.
     *
     * @since 1.0
     *
     */
    function wprss_et_get_default_settings_thumbnails() {

        // Set up the default thumbnail settings
        $settings = apply_filters( 
            'wprss_et_get_default_settings_thumbnails',
            array(
                'thumbnails_enable' => 1,  
                'default_thumbnail' => WPRSS_ET_IMG . 'default-thumbnail.png',
                'thumbnail_height'  => 150,
                'thumbnail_width'   => 175
            )
        );

        // Return the default settings
        return $settings;
    }    


    /**
     * Initialize settings to default ones if they are not yet set
     *
     * @since 1.0
     */
    function wprss_et_excerpts_settings_initialize() {
        // Get the settings from the field in the database, if it exists
        $settings = get_option( 'wprss_settings_excerpts' );

        if ( FALSE == $settings ) { 
            update_option( 'wprss_settings_excerpts', wprss_et_get_default_settings_excerpts() );
        }

        else {
            // Get the default plugin settings.
            $default_settings = wprss_et_get_default_settings_excerpts();

            // Loop through each of the default plugin settings. 
            foreach ( $default_settings as $setting_key => $setting_value ) {

                // If the setting didn't previously exist, add the default value to the $settings array. 
                if ( ! isset( $settings[ $setting_key ] ) )
                    $settings[ $setting_key ] = $setting_value;
            }

            // Update the plugin settings.
            update_option( 'wprss_settings_excerpts', $settings );  
        }     
    }


    /**
     * Returns an array of the default excerpt settings. Used for plugin activation
     *
     * @since 1.0
     */
    function wprss_et_get_default_settings_excerpts() {

        // Set up the default plugin settings
        $settings = apply_filters(
            'wprss_et_get_default_settings_excerpts',
            array(
                'excerpts_enable'     => 1,
                'excerpts_word_limit' => 50,
                'excerpts_ending'     => '...',
                'morelink_enable'     => 1,
                'excerpts_read_more'  => 'read more',
                'strip_html_tags'     => ''  
            )      
        );

        // Return the default settings
        return $settings;
    }        


    /**
     * Returns an array of the default license settings. Used for plugin activation.
     *
     * @since 1.1
     *
     */
    function wprss_et_get_default_settings_licenses() {

        // Set up the default license settings
        $settings = apply_filters( 
            'wprss_et_get_default_settings_licenses',
            array(
                'et_license_key' => FALSE,  
                'et_license_status' => 'invalid'
            )
        );

        // Return the default settings
        return $settings;
    }    


    /**
     * Initialize settings to default ones if they are not yet set
     *
     * @since 1.1
     */
    function wprss_et_licenses_settings_initialize() {
        // Get the settings from the database, if they exist
        $license_keys = get_option( 'wprss_settings_license_keys' );
        $license_statuses = get_option( 'wprss_settings_license_statuses' );
        $default_et_license_settings = wprss_et_get_default_settings_licenses();

        if ( FALSE == $license_keys && FALSE == $license_statuses ) { 
            $license_keys['et_license_key'] = $default_et_license_settings['et_license_key'];
            $license_statuses['et_license_status'] = $default_et_license_settings['et_license_status'];
            
            update_option( 'wprss_settings_license_keys', $license_keys );
            update_option( 'wprss_settings_license_statuses', $license_statuses );
        }

        else {

            if ( ! isset( $license_keys['et_license_key'] ) ) {
                $license_keys['et_license_key'] = $default_et_license_settings['et_license_key']; 
            }
            if ( ! isset( $license_statuses['et_license_status'] ) ) {
                $license_statuses['et_license_status'] = $default_et_license_settings['et_license_status']; 
            }

            // Update the plugin settings.
            update_option( 'wprss_settings_license_keys', $license_keys );  
            update_option( 'wprss_settings_license_statuses', $license_statuses );  
        }     
    }