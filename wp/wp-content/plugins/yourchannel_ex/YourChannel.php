<?php
/**
 * @package YourChannel
 * @version 0.8
 */
/*
	Plugin Name: YourChannel
	Plugin URI: http://plugin.builders/yourchannel/?from=plugins
	Description: YouTube channel on your website.
	Author: Plugin Builders
	Version: 0.8
	Author URI: http://plugin.builders/?from=plugins
	Text Domain: YourChannel
	Domain Path: languages
*/

if( !defined( 'ABSPATH' ) ) exit;

class WPB_YourChannel{
	static $version = '0.8';
	static $version_file = '0.8-pro';
	static $terms = array();
	static $playlist;
	static $st;
	static $so;
	
	function __construct(){
		self::translateTerms();
		
		register_activation_hook(__FILE__, array($this, 'onInstall'));
		$this->onInstall();
		
		add_action('admin_menu', array($this, 'createMenu'));
		add_action('admin_init', array($this, 'deploy'));
		add_action('plugins_loaded', array($this, 'loadTextDomain') );
		
		add_action('admin_enqueue_scripts', array($this, 'loadDashJs'));
		add_action('wp_enqueue_scripts', array($this, 'loadForFront'));
		
		add_action('wp_ajax_yrc_save', array($this, 'save'));
		add_action('wp_ajax_yrc_get', array($this, 'get'));
		add_action('wp_ajax_yrc_delete', array($this, 'delete'));
		add_action('wp_ajax_yrc_get_lang', array($this, 'getLang'));
		add_action('wp_ajax_yrc_save_lang', array($this, 'saveLang'));
		add_action('wp_ajax_yrc_delete_lang', array($this, 'deleteLang'));
		add_action('wp_ajax_yrc_clear_keys', array($this, 'clearKeys'));
		
		add_shortcode( 'yourchannel', array($this, 'shortcoode') );
		
		$this->premium();
	}
	
	public function onInstall(){
		update_option('yrc_version', WPB_YourChannel::$version);
	}	
	
	public function clearKeys(){
		$channels = (int)$_POST['yrc_content'];
		delete_option($channels ? 'yrc_keys' : 'yrc_playlist_keys');
		echo 1; die();
	}
	
	public function createMenu(){
		add_submenu_page(
			'options-general.php',
			'YourChannel',
			'YourChannel',
			'manage_options',
			'yourchannel',
			array($this, 'pageTemplate')
		);
	}
	
	public function pageTemplate(){ ?>
		<div class="wrap">
			<div id="icon-themes" class="icon32"></div>
			<h2 class="wpb-inline" id="yrc-icon">Your<span class="wpb-inline">Channel</span></h2>
			<div id="yrc-wrapper" data-version="<?php echo self::$version; ?>">
				<img src="<?php echo site_url('wp-admin/images/spinner.gif'); ?>" id="yrc-init-loader"/>
			</div>
		</div>
		<?php
		$this->templates();
	}
	
	public function templates(){
		do_action('yrc_templates');
		include 'templates/templates.php';
	}
	
	public function deploy(){}
	
	public function loadDashJs($hook){
		if($hook === 'settings_page_yourchannel'){
			wp_enqueue_script('wp-color-picker');
			wp_register_script('yrc_script', plugins_url('/js/yrc-'.self::$version_file.'.js', __FILE__), array('jquery', 'underscore', 'wp-color-picker'), null, 1);
			wp_enqueue_script('yrc_script');
			wp_register_script('yrc_admin_settings', plugins_url('/js/admin-'.self::$version_file.'.js', __FILE__), array('yrc_script'), null, 1);
			wp_enqueue_script('yrc_admin_settings');
			wp_register_style('yrc_admin_style', plugins_url('/css/admin-'.self::$version_file.'.css', __FILE__));
			wp_enqueue_style('yrc_admin_style');
			wp_register_style('yrc_style', plugins_url('/css/style-'.self::$version_file.'.css', __FILE__));
			wp_enqueue_style('yrc_style');
			wp_enqueue_style('wp-color-picker');
		}	
	}
	
	public function loadForFront(){}
	
	public static function nins( $array, $key ){	//nothing if not set
		return isset( $array[$key] ) && $array[$key] ? strtolower( $array[$key] ) : '';
	}
	
	public static function outputChannel( $user, $tag ){
		$user = strtolower( html_entity_decode($user) );
		$tag = strtolower( html_entity_decode($tag) );
		
		$keys = get_option('yrc_keys');
		$key = '';
		if(sizeof($keys) && is_array($keys)){
			foreach($keys as $k){
				if( ( strtolower( $k['user'] ) === $user ) && ( self::nins( $k, 'tag' ) === $tag ) ) {
					$key = $k['key']; break;
				}
			}	
		}
		return $key ? get_option( $key ) : '';
	}
	
	public static function outputPlaylist( $pl ){
		if(!$pl) return '';
		$pl = strtolower( html_entity_decode($pl) );
		
		$keys = get_option('yrc_playlist_keys');
		$key = '';
		if(sizeof($keys) && is_array($keys)){
			foreach($keys as $k){
				if( ( strtolower( $k['name'] ) === $pl ) ) {
					$key = $k['key']; break;
				}
			}	
		}
		return $key ? get_option( $key ) : '';
	}
	
	public function shortcoode($atts){
		$atts = shortcode_atts(
			array(
				'user' => '',
				'playlist' => '',
				'tag' => '',
				'search' => '',
				'own' => 1,
				'custom' => ''
			), $atts );
		return self::output( $atts['user'], $atts['playlist'],  $atts['tag'], $atts['custom'], $atts['search'], $atts['own']);
	}
	
	public static function output( $user = '', $playlist = '', $tag = '', $cu = '', $st = '', $so = '', $channel = array() ){ 
		self::$playlist = $playlist;
		self::$st = $st;
		self::$so = $so;
		$channel =  empty( $channel ) ? self::outputChannel( $user, $tag ) : $channel;
		if(!$channel) return '<span id="yrc-wrong-shortcode"></span>';
		$channel = apply_filters('yrc_output', $channel);
		
		$url = plugins_url('/js/yrc-'.self::$version_file.'.js', __FILE__);
		$css_url = plugins_url('/css/style-'.self::$version_file.'.css', __FILE__);
		
		
		self::translateTerms();
		$terms = array(
			'form' => get_option('yrc_lang_terms'),
			'fui' => self::$terms['front_ui']
		);
		
		
		$terms['form'] = $terms['form'] ? $terms['form'] : self::$terms['form'];
	
		return '<div class="yrc-shell-cover" data-yrc-channel="'. htmlentities( json_encode($channel) ) .'" data-yrc-setup="">'.
			($cu ? '<div class="yrc-cu-pl" data-cupl="'.htmlentities( json_encode( self::outputPlaylist( $cu ) ) ).'"></div>' : '')
		.'</div>
		<script data-cfasync="false" type="text/javascript">
			var YRC = YRC || {};
			(function(){
				if(!YRC.loaded){
					function YRC_Loader(){
						YRC.loaded = true;
						YRC.lang = '.json_encode( $terms ).';	
						var script = document.createElement("script");
							script.setAttribute("data-cfasync", "false");
							script.setAttribute("type", "text/javascript");
							script.src = "'.$url.'";
							script.id = "yrc-script";
							document.querySelector("head").appendChild(script);
						var style = document.createElement("link");
							style.rel = "stylesheet";
							style.href = "'.$css_url.'";
							style.type = "text/css";
							document.querySelector("head").appendChild(style);
					}
					if(window.jQuery){YRC_Loader()}else { var yrctimer2324 = window.setInterval(function(){
						if(window.jQuery){YRC_Loader(); window.clearInterval(yrctimer2324); }
					}, 250);}
				} else { if(YRC.EM)YRC.EM.trigger("yrc.newchannel");}
			}());
		</script>';
	}
	
	
	/**
	
		Input
		
	**/
	
	
	public function save(){
		$down = $this->validate( $_POST['yrc_channel'] );
		
		if(!$down['meta']['channel'] || !$down['meta']['apikey']) {echo 0; die();}
		
		$re = null;
		$key = $down['meta']['key'];
		$down['meta']['user'] = stripslashes( $down['meta']['user'] );
		$down['meta']['tag'] = stripslashes( $down['meta']['tag'] );
		if(isset( $down['css'] )) $down['css'] = stripslashes( $down['css'] );
				
		if($key === 'nw'){
			$re = get_option('yrc_keys');
			$re = $re ? $re : array();
			$key = 'yrc_'.time();
			$re[] = array('key'=>$key, 'user'=>$down['meta']['user'], 'tag'=>$down['meta']['tag']);
			$re = update_option('yrc_keys', $re);
			$down['meta']['key'] = $key;
			$re = update_option($key, $down);
			$re = $re ? $key : $re;
		} else {
			$re = get_option('yrc_keys');
			forEach($re as &$r){
				$tag = true;
				if(isset($r['tag']) && !empty($r['tag'])) $tag = ($r['tag'] === $down['meta']['tag']);
				if($r['user'] !== $down['meta']['user']) $tag = true;
				if( ($r['key'] === $down['meta']['key']) && $tag ) {
					$r['user'] = $down['meta']['user'];
					$r['tag'] = $down['meta']['tag'];
					update_option('yrc_keys', $re);
					$re = update_option($down['meta']['key'], $down);
					break;
				}
			}
			$re = $key ? $key : $re;
		}
		wp_send_json($re);
	}
		
	public function get(){
		$keys = get_option('yrc_keys');
		$re = array();
		if($keys){
			forEach($keys as $key){
				$re[] = get_option($key['key']);
			}
		}
		wp_send_json($re);
	}
	
	public function delete(){
		$key = sanitize_text_field( $_POST['yrc_key'] );
		$keys = get_option('yrc_keys');
		$re = false;
		forEach($keys as $i=>$k){
			if($k['key'] === $key) {
				unset($keys[$i]);
				update_option('yrc_keys', $keys);
				$re = delete_option( $key );
				break;
			}
		}	
		echo $re;
		die();
	}
	
	public function getLang(){
		wp_send_json( get_option('yrc_lang_terms') );
	}
	
	public function saveLang(){
		$lang = $_POST['yrc_lang'];
		echo update_option('yrc_lang_terms', $lang);
		die();
	}
	
	public function deleteLang(){
		delete_option('yrc_lang_terms');
		echo 1;
		die();
	}
		
	/**
	
		Sanitizing
		
	**/
	
	public $fields = array();
	
	public function validate($ins){
		$rins = $this->validation( $ins );
		return $rins;
	}
	
	public function validation( $ins ){
		$rins = array();
		foreach($ins as $key=>$value){
			$rins[$key] = $this->validateField( $key, $value );
		}
		return $rins;
	}
	
	public function validateField( $k, $val ){
		if(is_array($val)){
			$clean_val = $this->validation( $val );
		} else {
			$clean_val = $this->cleanse(
				( array_key_exists($k, $this->fields) ? $this->fields[$k] : 'string' ),
			$val);
		}
		return $clean_val;
	}
	
	public function cleanse($type, $value){
		switch($type){
			case 'int':
				return intval($value);
				break;
			case 'url':
				return esc_url($value);
				break;
			default:
				return sanitize_text_field($value);
				break;
		} 
	}
	
	public function loadTextDomain(){
		load_plugin_textdomain( 'YourChannel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}
	
	public static function translateTerms(){ 
		self::$terms['front_ui'] = array(
			'sort_by'  => __('Sort by', 'YourChannel'),
			'relevant'  => __('Relevant', 'YourChannel'),
			'latest'  => __('Latest', 'YourChannel'),
			'liked'  => __('Liked', 'YourChannel'),
			'title'  => __('Title', 'YourChannel'),
			'views'  => __('Views', 'YourChannel'),
			'duration'  => __('Duration', 'YourChannel'),
			'any'  => __('Any', 'YourChannel'),
			'_short'  => __('Short', 'YourChannel'),
			'medium'  => __('Medium', 'YourChannel'),
			'_long'  => __('Long', 'YourChannel'),
			'uploaded'  => __('Uploaded', 'YourChannel'),
			'all_time'  => __('All time', 'YourChannel'),
			'today'  => __('Today', 'YourChannel'),
			'ago'  => __('ago', 'YourChannel'),
			'last'  => __('Last', 'YourChannel'),
			'day'  => __('day', 'YourChannel'),
			'days'  => __('days', 'YourChannel'),
			'week'  => __('week', 'YourChannel'),
			'weeks'  => __('weeks', 'YourChannel'),
			'month'  => __('month', 'YourChannel'),
			'months'  => __('months', 'YourChannel'),
			'year'  => __('year', 'YourChannel'),
			'years'  => __('years', 'YourChannel'),
			'older'  => __('Older', 'YourChannel'),
			'wplocale' => get_locale()
		);
		
		self::$terms['form'] = array(
			'Videos'  => __('Videos', 'YourChannel'),
			'Playlists'  => __('Playlists', 'YourChannel'),
			'Search'  => __('Search', 'YourChannel'),
			'Loading'  => __('Loading', 'YourChannel'),
			'more'  => __('more', 'YourChannel'),
			'Nothing_found'  => __('Nothing found', 'YourChannel'),
			//'Prev' => __('Previous', 'YourChannel'),
			//'Next' => __('Next', 'YourChannel')
		);
	}
	
	
	/**		Pro Version Specific	**/
		
	public function premium(){
		
		new YourChannel_Updater(array(
			'key' => 'yrc_license_key',	
			'name_key' => 'yrc_yourchannel_name',	
			'status' => 'yrc_license_status',			
			'action' => 'yrc_license_action',			
			'version' => WPB_YourChannel::$version	
		));
		
		add_action('yrc_templates', array($this, 'proTemplate'));
		add_filter('yrc_output', array($this, 'proOutput'));
		add_action('wp_ajax_yrc_save_playlist', array($this, 'savePlaylist'));
		add_action('wp_ajax_yrc_get_playlists', array($this, 'getPlaylists'));
		add_action('widgets_init', array($this, 'regWidget'));
		
	}
		
	public function savePlaylist(){
		$down = $this->validate( $_POST['yrc_playlist'] );
				
		$re = null;
		$key = $down['key'];
		$down['name'] = stripslashes( $down['name'] );
				
		if($key === 'nw'){
			$re = get_option('yrc_playlist_keys');
			$re = $re ? $re : array();
			$key = 'yrc_playlist_'.time();
			$re[] = array('key'=>$key, 'name'=>$down['name']);
			$re = update_option('yrc_playlist_keys', $re);
			$down['key'] = $key;
			$re = update_option($key, $down);
			$re = $re ? $key : $re;
		} else {
			$re = get_option('yrc_playlist_keys');
			forEach($re as &$r){
				if( ($r['key'] === $down['key']) ) {
					$r['name'] = $down['name'];
					update_option('yrc_playlist_keys', $re);
					$re = update_option($down['key'], $down);
					break;
				}
			}
			$re = $key ? $key : $re;
		}
		wp_send_json($re);
	}
	
	public function getPlaylists(){
		$keys = get_option('yrc_playlist_keys');
		$re = array();
		if($keys){
			forEach($keys as $key){
				$re[] = get_option($key['key']);
			}
		}
		wp_send_json($re);
	}
	
	public function proOutput( $channel ){
		if(self::$playlist && !self::$st) $channel['meta']['playlist'] = sanitize_text_field( self::$playlist );
		if(self::$st) $channel['meta']['search_term'] = sanitize_text_field( self::$st );
		if(self::$st) $channel['meta']['search_own'] = self::$so;
		return $channel;
	}
		
	public function proTemplate(){
		include 'templates/pro_templates.php';
	}
	
	public function pluginInfoUpdates(){
		
	}
	
	public function regWidget(){
		register_widget('YourChannel_Widget');
	}
} 







class YourChannel_Updater{
	
		private $key;
		private $status;
		private $action;
		
		private $URL = 'http://plugin.builders/';
	
		function __construct( $args ){
										
			$this->key = $args['key'];			
			$this->name_key = $args['name_key'];			
			$this->status = $args['status'];			
			$this->action = $args['action'];			
			$this->version = $args['version'];	
			
			$this->name = get_option( $this->name_key );		
									
			if( !class_exists( 'EDD_SL_Plugin_Updater' ) ) {
				include( 'third/EDD_SL_Plugin_Updater.php' );
			}
			
			if( isset($_POST[ $this->key ]) && isset($_POST[ $this->action ]) ) $this->saveLicense();
			
			$this->check();
		}
		
		function check(){
			if( !$this->name ) return false;
			
			$license_key = trim( get_option( $this->key ) );
			
			$edd_updater = new EDD_SL_Plugin_Updater( $this->URL, __FILE__, array( 
					'version' 	=> $this->version, 		// current version number
					'license' 	=> $license_key,
					'item_name'     => $this->name,
					'author' 	=> 'Plugin Builders',
					'url'           => home_url()
				)
			);		
		}
		
		public function saveLicense(){
			$license = sanitize_text_field( $_POST[ $this->key ] );
			$action = sanitize_text_field( $_POST[ $this->action ] );
												
			$this->changeLicenseStatus( trim($license), $action );

		}
		
		public function retrieveName( $license ){
			$name = '';
			
			$params = array(
				'pbr_action' => 'get_name',
				'pbr_license' => $license
			);
			
			$request = wp_remote_post( $this->URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $params ) );
			
			if ( ! is_wp_error( $request ) ) {
				$request = json_decode( wp_remote_retrieve_body( $request ) );
				$name = $request->pbr_name;
				
				if( $name ) {
					$this->name = $name;
					update_option( $this->name_key, $this->name );
				}
				
				update_option($this->key, $license);			
			}
						
			if(!$name) update_option($this->status, 'invalid');			
		}
		
		public function changeLicenseStatus( $license, $action ){
						
			if( $action === 'Renew' ) $action = 'Activate';
			if( $action === 'Deactivate' ) $license =  get_option( $this->key );
			
			if( !$this->name ) $this->retrieveName( $license );
			if( !$this->name ) return false;

			// data to send in our API request
			$api_params = array(
				'edd_action'=> strtolower($action).'_license',
				'license' 	=> $license,
				'item_name' => urlencode( $this->name ), // the name of our product in EDD 
				'url'       => home_url()
			);
			 
			// Call the custom API.
			$response = wp_remote_post( $this->URL, array( 'timeout' => 15, 'sslverify' => false, 'body' => $api_params ) );

			// make sure the response came back okay
			if ( is_wp_error( $response ) )
				return false;

			// decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );
									
			// $license_data->license will be either "valid" or "invalid"
						
			update_option($this->key, $license);			
			update_option( $this->status, $license_data ? $license_data->license : 'invalid');
		}
		
}


















class YourChannel_Widget extends WP_Widget{

	public $bbh_id = 'yourchannel_widget';
	public $bbh_name = 'YourChannel';
	public $bbh_description = 'YouTube channel on your website.';
	
	function __construct(){
		parent::__construct(
			$this->bbh_id,
			__($this->bbh_name, 'text_domain'),
			array('description' => __($this->bbh_description, 'text_domain'))
		);
	}
	
	public function widget($args, $instance){
		echo $args['before_widget'];
		$instance['cust_playlist'] = isset($instance['cust_playlist']) ? $instance['cust_playlist'] : '';
		echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		?>
		<div class="pbc-wrapper <?php echo $instance['classes']; ?>">
			<?php echo WPB_YourChannel::output( $instance['key'], $instance['playlist'],  WPB_YourChannel::nins($instance, 'tag'), $instance['cust_playlist'] ); ?>
		</div>
		<?php 
		echo $args['after_widget'];
	} 
	
	public function form($instance){
		$title = empty($instance['title']) ? __('New Title', 'text_domain') : $instance['title'];
		$key = empty($instance['key']) ? '' : $instance['key'];
		$playlist = (!isset($instance['playlist']) || empty($instance['playlist'])) ? '' : $instance['playlist'];
		$tag = (!isset($instance['tag']) || empty($instance['tag'])) ? '' : $instance['tag'];
		$classes = (!isset($instance['classes']) || empty($instance['classes'])) ? '' : $instance['classes'];
		$cust_playlist = (!isset($instance['cust_playlist']) || empty($instance['cust_playlist'])) ? '' : $instance['cust_playlist'];
		?>
		
		<div>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" />
			<p></p>
		</div>
		
		<div>
			<label for="<?php echo $this->get_field_id('key'); ?>"><?php _e('Name:'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('key'); ?>" name="<?php echo $this->get_field_name('key'); ?>">
				<?php foreach(get_option('yrc_keys') as $k): ?>
					<option value="<?php echo $k['user']; ?>" <?php echo $k['user'] === $key ? 'selected' : ''; ?>><?php echo $k['user']; ?></option>
				<?php endforeach; ?>
			<select/><p></p>
			
			<label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag (optional):'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>">
				<option value=""></option>
				<?php foreach(get_option('yrc_keys') as $k): ?>
					<?php if(isset($k['tag']) && !empty($k['tag'])): ?>
						<option value="<?php echo $k['tag']; ?>" <?php echo $k['tag'] === $tag ? 'selected' : ''; ?>><?php echo $k['tag']; ?></option>
					<?php endif; ?>
				<?php endforeach; ?>
			<select/><p></p>
			
			<label for="<?php echo $this->get_field_id('cust_playlist'); ?>"><?php _e('Custom playlist (optional):'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('cust_playlist'); ?>" name="<?php echo $this->get_field_name('cust_playlist'); ?>">
				<option value=""></option>
				<?php $cp_keys = get_option('yrc_playlist_keys'); var_dump($cp_keys);
					if($cp_keys): ?>
						<?php foreach($cp_keys as $k): ?>
							<option value="<?php echo $k['name']; ?>" <?php echo $k['name'] === $cust_playlist ? 'selected' : ''; ?>><?php echo $k['name']; ?></option>
				<?php endforeach; endif; ?>
			<select/><p></p>
			
			<label for="<?php echo $this->get_field_id('playlist'); ?>"><?php _e('Playlist (optional):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('playlist'); ?>" name="<?php echo $this->get_field_name('playlist'); ?>" value="<?php echo $playlist; ?>" />
		</br></br>
			<label for="<?php echo $this->get_field_id('classes'); ?>"><?php _e('HTML Classes (optional):'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('classes'); ?>" name="<?php echo $this->get_field_name('classes'); ?>" value="<?php echo $classes; ?>" />
		</div>
		
		<?php
	}
	
	public function update($n, $o){
		$instance = array();
		$instance['title'] = $n['title'] ? $n['title'] : 'Title not Set';
		$instance['key'] = $n['key'] ? $n['key'] : 'Please select one';
		$instance['playlist'] = $n['playlist'] ? $n['playlist'] : '';
		$instance['cust_playlist'] = $n['cust_playlist'] ? $n['cust_playlist'] : '';
		$instance['tag'] = $n['tag'] ? $n['tag'] : '';
		$instance['classes'] = $n['classes'] ? $n['classes'] : '';
		return $instance;
	}
}

new WPB_YourChannel();
?>
