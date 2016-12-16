<?php
		/*
		Plugin Name: Marcador - Widget News
		Description: Genera Un bloque de información
		Author: Raylin Aquino
		Author URI: http://raylinaquino.com
		Version: 1.0
		*/

		class MarWidNews extends WP_Widget {

			public $type_news;

			function __construct() {
				parent::__construct(false,$name = __("Marcador - Widget News"), false, false);

				$this->type_news = array(
					'1' => __('una columna'),
					'2' => __('2 columnas: 4 noticias'),
					'3' => __('2 columnas: 2 noticias'),
					'4' => __('1 columna: 4 noticias'),
					'5' => __('1 columna con resultado')
					);
			}


			function widget( $args, $instance ) {
				extract( $args, EXTR_SKIP );

				global $wp_query;

				$show_title = sanitize_text_field($instance["show_title"]);
				$show_excerpt = sanitize_text_field($instance["show_excerpt"]);
				$show_date = sanitize_text_field($instance["show_date"]);
				$show_author = sanitize_text_field($instance["show_author"]);
				$excerpt_limit = (empty($instance["excerpt_limit"])) ? 999 : $instance["excerpt_limit"];
				$post_limit = (empty($instance["post_limit"])) ? 1 : $instance["post_limit"];
				$type_news = (empty($instance["type_news"])) ? 1 : $instance["type_news"];
				$category = $instance["category"];
				$tag = $instance["tag"];

				$equipo1_title = sanitize_text_field($instance["equipo1_title"]);
				$equipo2_title = sanitize_text_field($instance["equipo2_title"]);
				$equipo1_resultado = sanitize_text_field($instance["equipo1_resultado"]);
				$equipo2_resultado = sanitize_text_field($instance["equipo2_resultado"]);
				$equipo_resultado = sanitize_text_field($instance["equipo_resultado"]);


				$orden = (empty($instance["orden"])) ? 'date' : $instance["orden"];
				$orden_type = (empty($instance["orden_type"])) ? 'DESC' : $instance["orden_type"];



				$args = array(
					'posts_per_page' => $post_limit,
					'orderby ' => $orden,
					'order' => $orden_type 
					);

				if(!empty($category)){
					$args['cat'] = $category; 
				}
				if(!empty($tag)){
					$args['tag_id'] = $tag; 
				}

				$query_posts = new WP_Query($args);

				if($query_posts->coun){
					__e('No se han encontrado resultados.');
					return false;
				}

				$cls_sidebar = "sidebar_".sanitize_title($args["name"]);
				?>
				<!--▼▼▼ Widget News ▼▼▼-->

				<?php 

				
				if($type_news == "1"){
					while( $query_posts->have_posts()): $query_posts->the_post(); 
					?>



					<!-- Marcador Hero Post -->
					<article class="row marcador-hero-post">
						<div class="col-xs-12">
							<a class="marcador-hero-permalink" href="<?php the_permalink(); ?>">
								<header class="marcador-hero-unit" style="background-image: url('<?php echo the_post_thumbnail_url(); ?>');">
									<?php 
									if($show_title != '1'):
										?>
									<h1 class="heading">
										<?php the_title(); ?>
									</h1>
								<?php endif; ?>
							</header>
						</a>
						<p>
							<?php 
							if($show_author != '1'):
								?>
							<span class="author"><?php _e('Por'); ?> <?php the_author_posts_link(); ?></span> 
						<?php endif; ?>

						<?php 
						if($show_date != '1'):
							?>
						<span class="date"><?php the_date('M d, Y'); ?></span>
					<?php endif; ?>
				</p>
				<?php 
				if($show_excerpt != '1'):

					echo wp_trim_words(get_the_excerpt(),$excerpt_limit);
				endif;
				?>

			</div>
		</article>
		<?php 
		endwhile; 
	}  else if($type_news == "2") { ?>
<div class="row">
	<?php
		while( $query_posts->have_posts()): $query_posts->the_post(); 

		$categories = get_the_category();

		?>
		<div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 marcador-post-list">
			<div class="row">
				<div class="col-xs-4 col-sm-5 marcador-post-list-image-col">
					<a href="<?php the_permalink(); ?>">
						<?php if ( has_post_thumbnail($post->ID) ): ?>
						<div class="marcador-post-list-image" style="background-image: url('<?php the_post_thumbnail_url("thumbnail"); ?>');">
						</div>
					<?php else: ?>
					<div class="marcador-post-list-image"></div>
				<?php endif; ?>
			</a>
		</div>
		<div class="col-xs-8 col-sm-7">
			<div class="marcador-post-list-content">
				
				<div class="marcador-post-list-category">
					<?php foreach($categories as $cat): 
					if($cat->term_id != $category) {
						continue;
					}
					?>
					<a href="<?php echo get_category_link($cat->term_id); ?>" rel="category tag"><?php echo $cat->name; ?></a>
				<?php endforeach; ?>
			</div>
		<?php if($show_title != '1'): ?>
			<div class="marcador-post-list-title">
				<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
			</div>
		<?php endif; ?>

			<div class="marcador-post-list-meta">
				<?php if($show_author != '1'): ?>
				<div class="marcador-post-list-author">
					<?php the_author_posts_link(); ?>
				</div>
			<?php endif; ?>
				<?php if($show_date != '1'): ?>
				<div class="marcador-post-list-date">
					<a href="#date-link">
						<?php the_date('M d, Y'); ?>
					</a> 
				</div>
			<?php endif; ?>
				<?php 
                  // Check user session
				if ( is_user_logged_in() ): 
                    // Check user role
					$user = new WP_User( $user_ID );
				$marcador_user_role = 'marcador_contributor';
				$is_colaborator = array_search(
					$marcador_user_role, 
					$user->roles, true
					);
				if ($is_colaborator !== false && $is_colaborator >= 0):
                      // TODO: Check Favoritos
					?>
				<!-- Conditional -->
				<div class="marcador-post-list-fav">
					<i class="material-icons">star</i>
				</div>
				<!-- end conditional -->
			<?php endif; ?>
		<?php endif; ?>
	</div>
	<?php if($show_excerpt != '1'): ?>
	<div class="marcador-post-list-excerpt">
		<?php echo wp_trim_words(get_the_excerpt(),$excerpt_limit); ?>
	</div>
<?php endif; ?>
</div>
</div>
</div>
</div>	


<?php endwhile;?>
</div>
<?php
}  
else if($type_news == "4") { ?>


	<!-- Marcador posts -->
		<div class="row marcador-post-list-full-row">

			<?php while( $query_posts->have_posts()): $query_posts->the_post();  ?>

			<div class="col-xs-12 marcador-post-list">
					<div class="row">
						<div class="col-xs-4 col-sm-4 col-md-4 col-lg-2 marcador-post-list-image-col">
							<a href="<?php echo esc_url( get_permalink() ); ?>">
								<?php if( has_post_thumbnail( get_the_id()) ): ?>
								<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'full' ); ?>
							<?php endif; ?>
							<div class="marcador-post-list-image" style="background-image: url('<?php echo $image[0]; ?>') "></div>
						</a> 
					</div>
					<div class="col-xs-8 col-sm-8 col-md-8 col-lg-10">
						<div class="marcador-post-list-content">
							<?php 
							$categories = get_the_category();
							$category = $categories[0]->name; 
	                	$category_id = $categories[0]->term_id; // var_dump($categories[0]); 
	                	$cat_count = count( $categories ) - 1;
	                	$c = 0;
	                	?>
	                	<?php if( $cat_count > 1 ): ?>
	                	<div class="marcador-post-list-category">
	                		<?php foreach ($categories as $cat => $cat_value):  ?>
	                		<?php if( $cat_value->slug != 'acento' ): ?>
	                		<a href="<?php echo esc_url( get_category_link( $cat_value->term_id ) ); ?>">
	                			<?php echo $cat_value->name; ?>
	                		</a>
	                		<?php if( ++$c !== $cat_count ): ?>,<?php 
	                		endif; ?>
	                	<?php endif; ?>
	                <?php endforeach; ?>
	            </div>
	        <?php endif; ?>

	        <?php if($show_title != '1'): ?>
	        <div class="marcador-post-list-title">
	        	<a href="<?php echo esc_url( get_permalink() ); ?>">
	        		<?php the_title(); ?>
	        	</a>
	        </div>
	    <?php endif; ?>
	        <?php if($show_excerpt != '1'): ?>
	        <div class="marcador-post-list-excerpt">
	        	<?php echo wp_trim_words(get_the_excerpt(),$excerpt_limit); ?>
	        </div>
	    <?php endif; ?>
	        <div class="marcador-post-list-meta">
	        	<?php if($show_author != '1'): ?>
	        	<div class="marcador-post-list-author">
	        		<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>">
	        			<?php echo get_the_author_meta( 'user_nicename' ); ?>
	        		</a>
	        	</div>
	        <?php endif; ?>
	        	<?php if($show_authordate != '1'): ?>
	        	<div class="marcador-post-list-date">
	        		<a href="<?php echo esc_url( get_day_link( $year = get_the_date('Y') , $month = get_the_date('m'), $day = get_the_date('d') ) ) ?>">
	        			<?php the_date('M d, Y', '<div class="meta-divisor"></div>', ''); ?>
	        		</a> 
	        	</div>
	        <?php endif; ?>
	        	<!-- Conditional if favorite -->
	        	<div class="marcador-post-list-fav">
	        		<i class="material-icons">star</i>
	        	</div>
	        	<!-- end conditional -->
	        </div>
	    </div>
	</div>
</div>
</div>

<?php endwhile; ?>



</div>
<!-- .marcador-posts-listing -->
</div>


<?php }  else if($type_news == "3") { ?>
<div class="row">
<?php while( $query_posts->have_posts()): $query_posts->the_post();  ?>
<div class="col-xs-12 col-sm-6 col-md-6 col-lg-6 marcador-post-list card">
          <div class="panel panel-default">
            <div class="panel-heading">
              <div class="marcador-post-list-title">
                <a href="<?php echo esc_url( get_permalink() ); ?>">
                  <?php the_title(); ?>
                </a>
              </div>
            </div>
            <div class="panel-body">
              <?php 
                $categories   = get_the_category();
                $category     = $categories[0]->name; 
                $category_id  = $categories[0]->term_id; // var_dump($categories[0]); 
                $cat_count    = count( $categories ) - 1;
                $c            = 0; ?>
                <?php if( $cat_count > 1 ): ?>
                  <div class="marcador-post-list-category">
                    <?php foreach ($categories as $cat => $cat_value):  ?>
                      <?php if( $cat_value->slug != 'acento' ): ?>
                        <a href="<?php echo esc_url( get_category_link( $cat_value->term_id ) ); ?>">
                          <?php echo $cat_value->name; ?>
                        </a>
                        <?php if( ++$c !== $cat_count ): ?>,<?php endif; ?>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
            </div>
            <a href="<?php echo esc_url( get_permalink() ); ?>">
              <?php if( has_post_thumbnail( get_the_id()) ): ?>
                <div class="panel-body marcador-post-list-image" style="background-image: url('<?php echo $image[0]; ?>')">
                  <?php $image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_id() ), 'full' ); ?>
                </div>
              <?php endif; ?>
            </a> 
            <?php if($show_excerpt != '1'): ?>
            <div class="panel-body">
              <div class="marcador-post-list-excerpt">
                <?php echo wp_trim_words(get_the_excerpt(),$excerpt_limit); ?>
              </div>
            </div>
        <?php endif; ?>
            <div class="panel-footer">
              <div class="marcador-post-list-meta">
                  <div class="row">
                    <?php if($show_author != '1'): ?>
                    <div class="col-xs-6 col-md-4 col-md-push-4 marcador-post-list-author">
                      
                        <a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ), get_the_author_meta( 'user_nicename' ) ); ?>">
                          <?php echo get_the_author_meta( 'user_nicename' ); ?>
                        </a>
                      
                    </div>
                <?php endif; ?>
                      <div class="col-xs-6 col-md-4 col-md-push-4 marcador-post-list-date">
                    <?php 
                    $before = ''; 
                    $before .= '<a href="' . esc_url( get_day_link( $year = get_the_date('Y') , $month = get_the_date('m'), $day = get_the_date('d') ) ) . '">';
                    $after = '</a>';
                    ?>
              
                    <?php 
                    if($show_date != '1'):
                    	the_date( $d = 'M d, Y', $before, $after, $echo = true );
                    endif;
                    ?>
                          <i class="material-icons marcador-post-list-fav">star</i></div>
                  </div>
              </div>
              
              <!-- Conditional if favorite -->
                
              <!-- end conditional -->

            </div>

    </div>
</div> 
<?php endwhile; ?>

</div>


<?php } else if($type_news == "5"){
while( $query_posts->have_posts()): $query_posts->the_post();
 ?>
	<!-- Marcador Hero Post Score -->
<div class="row marcador-hero-post score">
      <div class="col-xs-12">
        <header class="marcador-hero-unit" style="background-image: url('<?php echo the_post_thumbnail_url(); ?>');">
            <!-- Score Board -->
            <div class="scoreboard">
                           
					<span class='team-one-title'><?php echo $equipo1_title; ?></span>
					    <div class="board">
					    <div><span><?php echo $equipo1_resultado; ?></span><span> <?php echo $equipo2_resultado; ?></span></div>
					    <h6><?php echo $equipo_resultado; ?></h6>
					    </div>
					<span class='team-two-title'><?php echo $equipo2_title; ?></span>
			</div>
			   <!-- end Score Board -->
          </header>
        <a href="<?php the_permalink(); ?>" class="marcador-hero-permalink">
          <div class="hero-heading">
            <h1 class="heading"><?php the_title(); ?></h1>
            <p class="excerpt"><?php echo wp_trim_words(get_the_excerpt(),$excerpt_limit); ?></p>
          </div>
        </a>
      </div>
    </div>
<!-- /.marcador-hero-post.score -->
<?php endwhile; } ?>

<!-- /.marcador-hero-post -->

<!--▲▲▲ end Widget News ▲▲▲-->

<?php
wp_reset_query();
}

function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance["show_title"] = $new_instance["show_title"];
	$instance["show_excerpt"] = $new_instance["show_excerpt"];
	$instance["show_date"] = $new_instance["show_date"];
	$instance["show_author"] = $new_instance["show_author"];
	$instance["post_limit"] = $new_instance["post_limit"];
	$instance["excerpt_limit"] = $new_instance["excerpt_limit"];
	$instance["orden"] = $new_instance["orden"];
	$instance["orden_type"] = $new_instance["orden_type"];
	$instance["category"] = $new_instance["category"];	
	$instance["tag"] = $new_instance["tag"];	
	$instance["type_news"] = $new_instance["type_news"];
	$instance["custom_class"] = $new_instance["custom_class"];
	$instance["widget_title"] = $new_instance["widget_title"];
	$instance["equipo1_title"] = $new_instance["equipo1_title"];
	$instance["equipo2_title"] = $new_instance["equipo2_title"];
	$instance["equipo1_resultado"] = $new_instance["equipo1_resultado"];
	$instance["equipo2_resultado"] = $new_instance["equipo2_resultado"];
	$instance["equipo_resultado"] = $new_instance["equipo_resultado"];



	echo $instance["widget_title"];


	return $instance;
}


function form( $instance ) {
	$show_title = sanitize_text_field($instance["show_title"]);
	$show_excerpt = sanitize_text_field($instance["show_excerpt"]);
	$show_date = sanitize_text_field($instance["show_date"]);
	$show_author = sanitize_text_field($instance["show_author"]);
	$excerpt_limit = sanitize_text_field($instance["excerpt_limit"]);
	$orden = sanitize_text_field($instance["orden"]);
	$orden_type = sanitize_text_field($instance["orden_type"]);
	$category = sanitize_text_field($instance["category"]);
	$tag = sanitize_text_field($instance["tag"]);

	$custom_class = sanitize_text_field($instance["custom_class"]);
	$post_limit = $instance["post_limit"];
	$type_news = $instance["type_news"];
	$widget_title = $instance["widget_title"];
	$equipo1_title = $instance["equipo1_title"];
	$equipo2_title = $instance["equipo2_title"];
	$equipo1_resultado = $instance["equipo1_resultado"];
	$equipo2_resultado = $instance["equipo2_resultado"];
	$equipo_resultado = $instance["equipo_resultado"];






	$orders = array(
		'none',
		'ID',
		'author',
		'title',
		'name',
		'type',
		'date',
		'modified',
		'parent',
		'rand',
		'comment_count'
		);

	$orders_type = array(
		'ASC','DESC'
		);

	$cats = get_categories();
	$tags = get_tags();

	?>


	<style>

	.widget-news-backend label{
		display: block;

	}
	</style>
	<div class="widget-news-backend">
		<h2><?php _e('Widget de noticias') ?> </h2>
		<small><?php _e('Puedes seleccionar y filtrar noticias como gustes.'); ?></small>
		<p>
			<label><?php _e('Titulo del Widget') ?>
				<input class="widefat" placeholder="<?php _e('Solo con fines de ejemplificar'); ?>" 
				name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" 
				<?php echo checked($widget_title); ?> value="<?php echo $widget_title; ?>" /> 
			</label>
		</p>
		<p>
			<label><?php _e('Limite') ?>
				<input class="widefat" placeholder="<?php _e('Por defecto: 1'); ?>" name="<?php echo $this->get_field_name('post_limit'); ?>" type="text" <?php echo checked($post_limit); ?> value="<?php echo $post_limit; ?>" /> 
			</label>
		</p>
		

		<p>
			<label><?php _e('Excerpt Limite') ?> <small><?php _e('Especifica un limite de palabras') ?></small>
				<input class="widefat" placeholder="<?php _e('Por defecto: sin limite'); ?>" name="<?php echo $this->get_field_name('excerpt_limit'); ?>" type="number" <?php echo checked($post_limit); ?> value="<?php echo $excerpt_limit; ?>" /> 
			</label>
		</p>

		<p>
			<label><?php _e('Elegir el estilo') ?>: <br />
				<select name="<?php echo $this->get_field_name('type_news'); ?>" class='widefat'>
					<option value="date"><?php _e('- Selecciona -'); ?></option>
					<?php 
					foreach($this->type_news as $key => $tipe):
						?>
					<option value="<?php echo $key; ?>" <?php selected( $key, $type_news ); ?>><?php echo $tipe; ?></option>
				<?php endforeach; ?>

			</select>
		</label>
	</p>
	<p>
		<label><?php _e('Orden') ?>: <br />
			<select name="<?php echo $this->get_field_name('orden'); ?>" class='widefat'>
				<option value="date"><?php _e('- Selecciona -'); ?></option>
				<?php 
				foreach($orders as $ord):
					?>
				<option value="<?php echo $ord; ?>" <?php selected( $ord, $orden ); ?>><?php echo $ord; ?></option>
			<?php endforeach; ?>

		</select>
	</label>
</p>
<p>
	<label><?php _e('Ordenar por') ?>: <br />
		<select name="<?php echo $this->get_field_name('orders_type'); ?>" class='widefat'>
			<option value="DESC"><?php _e('- Selecciona -'); ?></option>
			<?php 
			foreach($orders_type as $ord):
				?>
			<option value="<?php echo $ord; ?>" <?php selected( $ord, $orden_type ); ?>><?php echo $ord; ?></option>
		<?php endforeach; ?>

	</select>
</label>
</p>




<p>
	<label><?php _e('Categorias') ?>: <small><?php _e('Selecciona una.') ?></small> <br />
		<select name="<?php echo $this->get_field_name('category'); ?>" class='widefat'>
			<option value="date"><?php _e('- Selecciona -'); ?></option>
			<?php 
			foreach($cats as $cat):
				?>
			<option value="<?php echo $cat->term_id; ?>" <?php selected( $cat->term_id, $category ); ?>><?php echo $cat->name; ?></option>
		<?php endforeach; ?>

	</select>
</label>
</p>

<p>
	<label><?php _e('Etiquetas') ?>: <small><?php _e('Selecciona una.') ?></small> <br />
		<select name="<?php echo $this->get_field_name('tag'); ?>" class='widefat'>
			<option value="date"><?php _e('- Selecciona -'); ?></option>
			<?php 
			foreach($tags as $tg):
				?>
			<option value="<?php echo $tg->term_id; ?>" <?php selected( $tg->term_id, $tag ); ?>><?php echo $tg->name; ?></option>
		<?php endforeach; ?>

	</select>
</label>
</p>

<hr>
<h2><?php _e('Solo para columnas con resultados'); ?></h2>
<h3><?php _e('Equipo 1') ?></h3>
	<p>
			<label><?php _e('Título pie del resultado') ?>
				<input class="widefat"  
				name="<?php echo $this->get_field_name('equipo_resultado'); ?>" type="text" 
				<?php echo checked($equipo_resultado); ?> value="<?php echo $equipo_resultado; ?>" /> 
			</label>
		</p>
<table width='100%'>
	<tr>
		<td>
				<p>
			<label><?php _e('Título') ?>
				<input class="widefat"  
				name="<?php echo $this->get_field_name('equipo1_title'); ?>" type="text" 
				<?php echo checked($equipo1_title); ?> value="<?php echo $equipo1_title; ?>" /> 
			</label>
		</p>
		</td>
		<td>
			<p>
			<label><?php _e('Resultado') ?>
				<input class="widefat"  
				name="<?php echo $this->get_field_name('equipo1_resultado'); ?>" type="text" 
				<?php echo checked($equipo1_resultado); ?> value="<?php echo $equipo1_resultado; ?>" /> 
			</label>
		</p>
		</td>
	</tr>
</table>
<h3><?php _e('Equipo 2') ?></h3>
	<table width='100%'>
	<tr>
		<td>
				<p>
			<label><?php _e('Título') ?>
				<input class="widefat"  
				name="<?php echo $this->get_field_name('equipo2_title'); ?>" type="text" 
				<?php echo checked($equipo2_title); ?> value="<?php echo $equipo2_title; ?>" /> 
			</label>
		</p>
		</td>
		<td>
			<p>
			<label><?php _e('Resultado') ?>
				<input class="widefat"  
				name="<?php echo $this->get_field_name('equipo2_resultado'); ?>" type="text" 
				<?php echo checked($equipo2_resultado); ?> value="<?php echo $equipo2_resultado; ?>" /> 
			</label>
		</p>
		</td>
	</tr>
</table>
	
<hr>


<p>
	<label>
		<input class="widefat" name="<?php echo $this->get_field_name('show_title'); ?>" type="checkbox" <?php echo checked($show_title); ?> value="1" /> <?php _e('¿Ocultar título?') ?>
	</label>
</p>
<p>
	<label>
		<input class="widefat" name="<?php echo $this->get_field_name('show_date'); ?>" type="checkbox" <?php echo checked($show_date); ?> value="1" /> <?php _e('¿Ocultar fecha?') ?>: 
	</label>
</p>
<p>
	<label>
		<input class="widefat" name="<?php echo $this->get_field_name('show_author'); ?>" type="checkbox" <?php echo checked($show_author); ?> value="1" /> <?php _e('¿Ocultar autor?') ?>: 
	</label>
</p>
<p>
	<label>
		<input class="widefat" name="<?php echo $this->get_field_name('show_excerpt'); ?>" type="checkbox" <?php echo checked($show_excerpt); ?> value="1" /> <?php _e('¿Ocultar excerpt?') ?>: 
	</label>
</p>


</div>

<?php
}
}

add_action( 'widgets_init', create_function( '', "register_widget( 'MarWidNews' );" ) );



?>