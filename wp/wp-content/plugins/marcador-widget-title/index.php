<?php
		/*
		Plugin Name: Narcador - Widget Title
		Description: Genera un widget con titulos especificos
		Author: Raylin Aquino
		Author URI: http://raylinaquino.com
		Version: 1.0
		*/

		class MarWidTitle extends WP_Widget {
			public $common_label = "marcador-widget-title"; /*Nombre común del plugin*/

			function __construct() {
				parent::__construct(false,$name = __("Narcador - Widget Title"), false, false);

			}


			function widget( $args, $instance ) {
				extract( $args, EXTR_SKIP );

				global $wp_query;
	
				$titulo = $instance["titulo"];
				$subtitulo = $instance["subtitulo"];
				
				$cls_sidebar = "sidebar_".sanitize_title($args["name"]);
				?>

				<!--▼▼▼ Widget Block Title ▼▼▼-->				
			  
					<div class="section-title-block <?php echo $common_label; ?>">
					 <h2 class="page-title"><?php echo $titulo; ?> <span><?php echo $subtitulo; ?></span></h2>
					</div>
				<!--▲▲▲ end Widget Block Title  ▲▲▲-->

				<?php
				wp_reset_query();
			}

			function update($new_instance, $old_instance) {
				$instance = $old_instance;
				$instance["titulo"] = $new_instance["titulo"];
				$instance["subtitulo"] = $new_instance["subtitulo"];

				return $instance;
			}


			function form( $instance ) {
				$titulo = $instance['titulo'];
				$subtitulo = $instance['subtitulo'];

				?>

	<style>

	.widget-title-backend label{
		display: block;

	}
	</style>
		<div class="widget-title-backend">
				<p>
				    <label><?php _e('Título'); ?><br>
				    <input class="widefat" name="<?php echo $this->get_field_name('titulo'); ?>" type="text" value="<?php echo attribute_escape($titulo); ?>" />
					</label>
				</p>
				<p>
				    <label><?php _e('Subtítulo'); ?><br>
				    <input class="widefat" name="<?php echo $this->get_field_name('subtitulo'); ?>" type="text" value="<?php echo attribute_escape($subtitulo); ?>" />
					</label>
				</p>
	</div>
				<?php
			}
		}

		add_action( 'widgets_init', create_function( '', "register_widget( 'MarWidTitle' );" ) );

?>