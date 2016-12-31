<?php
// If uninstall not called from WordPress exit
if( ! defined( 'WP_UNINSTALL_PLUGIN' ) )
exit ();

// Delete option from options table
delete_option( 'wprss_settings_excerpts' );
delete_option( 'wprss_settings_thumbnails' );
delete_option( 'wprss_et_db_version' );