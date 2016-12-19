<?php
		/*
		Plugin Name: Narcador - Widget Statistics
		Description: Genera un widget
		Author: Raylin Aquino
		Author URI: http://raylinaquino.com
		Version: 1.0
		*/

		class MarWidStat extends WP_Widget {
			public $common_label = "marcador-widget-statistics"; /*Nombre común del plugin*/

			function __construct() {
				parent::__construct(false,$name = __("Narcador - Widget Statistics"), false, false);

			}


			function widget( $args, $instance ) {
				extract( $args, EXTR_SKIP );

				global $wp_query;
	
				$titulo = $instance["titulo"];
			
				include(get_template_directory().'/includes/marcador_cintillo_estadisticas.include.php');

				wp_reset_query();
			}

			function update($new_instance, $old_instance) {
				$instance = $old_instance;
				$instance["titulo"] = $new_instance["titulo"];

				return $instance;
			}


			function form( $instance ) {
				$titulo = $instance['titulo'];

			}
		}

		add_action( 'widgets_init', create_function( '', "register_widget( 'MarWidStat' );" ) );

?>