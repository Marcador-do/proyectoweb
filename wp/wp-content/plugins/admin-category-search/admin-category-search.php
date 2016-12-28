<?php

/*
Plugin Name: Admin category search
Plugin URI: https://github.com/atillaordog/wp-admin-search
Description: This plugin adds a search tab to the category boxes on admin add / edit pages, thus making it easy to search a category in a very long list
Version: 0.4
Author: Atilla Ordog
Author URI: http://www.gadratilproramming.net
*/

function register_admin_search_category_custom_script($hook)
{	
	if ( 'post.php' != $hook && $hook != 'post-new.php' )
	{
		return;
	}	
	
	wp_register_script('admin_category_search', site_url().'/wp-content/plugins/admin-category-search/js/script.js');
	wp_enqueue_script('admin_category_search', false, array(), false, true);
}

add_action( 'admin_enqueue_scripts', 'register_admin_search_category_custom_script');