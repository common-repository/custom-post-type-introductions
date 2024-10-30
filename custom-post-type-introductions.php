<?php
/**
* @package JCK_Custom_PT_Intros
* @version 1.0.0
**/

/*

Plugin Name: Custom Post Type Introductions
Plugin URI: http://wordpress.org/extend/plugins/admin-quick-jump/
Description: Adds a page to all public custom post types to allow for an introduction.
Author: James Kemp
Version: 1.0.1
Author URI: http://www.jckemp.com/

*/

class jck_cpti {

/* 	=============================
   	Subpages
   	============================= */

	function create_subpages() {
		
		$args = array(
			'public'   => true,
			'publicly_queryable' => true,
			'_builtin' => false
		); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args,$output,$operator); 
		
		foreach($post_types as $post_type){
			$PT_Name = $this->get_posttype_title($post_type);
			add_submenu_page( 'edit.php?post_type='.$post_type, $PT_Name.' Introduction', 'Introduction', 'manage_options', 'introduction_'.$post_type, array(&$this, 'introduction_callback') ); 
		}
		
	}
	
	function introduction_callback() { ?>
		
		<div class="wrap">
			<?php $PT_Name = $this->get_posttype_title($_GET['post_type']);		
			echo '<h2>'.$PT_Name.' Introduction</h2>'; ?>
			
			<?php do_action('jck_admin_notices'); ?>
			
			<form action="options.php" method="post">
				<?php settings_fields($_GET['post_type'].'_options'); ?>
				<?php do_settings_sections('plugin'); ?>
		 
				<p><input name="Submit" type="submit" value="<?php esc_attr_e('Save Changes'); ?>" class="button button-primary" /></p>
			</form>
		</div>
	
	<?php 
	}
	
/* 	=============================
   	Settings Fields 
   	============================= */
	
	/** Register Settings **/
	function register_settings(){
		
		$args = array(
			'public'   => true,
			'publicly_queryable' => true,
			'_builtin' => false
		); 
		$output = 'names'; // names or objects, note names is the default
		$operator = 'and'; // 'and' or 'or'
		$post_types = get_post_types($args,$output,$operator);
		
		foreach($post_types as $post_type){
			register_setting( $post_type.'_options', $post_type.'_options', array(&$this, 'plugin_options_validate') );
		}
		
		if(isset($_GET['post_type'])) {
			$PT_Name = $this->get_posttype_title($_GET['post_type']);
			
			add_settings_section('plugin_main', false, false, 'plugin');
			add_settings_field('plugin_title', 'Title', array( &$this, 'title_field' ), 'plugin', 'plugin_main');
			add_settings_field('posttype_content', 'Introduction', array( &$this, 'content_field' ), 'plugin', 'plugin_main');
		}
	}
	
	/** Settings Field Contents **/
	function title_field() {
		$options = get_option($_GET['post_type'].'_options');
		echo "<input id='".$_GET['post_type']."_title' name='".$_GET['post_type']."_options[title]' size='40' type='text' value='".(is_array($options) && array_key_exists('title', $options) ? $options['title'] : "")."' />";
	}
	
	function content_field() {
		$options = get_option($_GET['post_type'].'_options');
		$settings = array(
		    'teeny' => false,
		    'textarea_rows' => 15,
		    'tabindex' => 1,
		    'textarea_name' => $_GET['post_type']."_options[content]"
		);
		wp_editor( (is_array($options) && array_key_exists('content', $options) ? $options['content'] : ""), "posttype_content", $settings);
	}
	
	/** Settings Field Validation **/
	function plugin_options_validate($input) {
		$newinput = array();
		$newinput['title'] = trim($input['title']);
		$newinput['content'] = $input['content'];
		return $newinput;
	}
	
/* 	=============================
   	Admin Notices 
   	============================= */
	
	function jck_show_msg($message, $msgclass = 'info') {
		echo "<div id='message' class='$msgclass below-h2'>$message</div>";
	}
	
	function admin_msgs() {	
		// collect setting errors/notices: //http://codex.wordpress.org/Function_Reference/get_settings_errors
		$set_errors = get_settings_errors(); 
		
		//display admin message only for the admin to see, only on our settings page and only when setting errors/notices are returned!	
		if(current_user_can ('manage_options') && !empty($set_errors)){
	
			// have our settings succesfully been updated? 
			if($set_errors[0]['code'] == 'settings_updated' && isset($_GET['settings-updated'])){
				$this->jck_show_msg("<p>" . $set_errors[0]['message'] . "</p>", 'updated');
			
			// have errors been found?
			} else {
				// there maybe more than one so run a foreach loop.
				foreach($set_errors as $set_error){
					// set the title attribute to match the error "setting title" - need this in js file
					$this->jck_show_msg("<p class='setting-error-message' title='" . $set_error['setting'] . "'>" . $set_error['message'] . "</p>", 'error');
				}
			}
		}
	}
	
/* 	=============================
   	Shortcodes 
   	============================= */
	
	// [post_type_intro field="title" posttype=""]
	function post_type_intro_func( $atts ) {
		
		extract( shortcode_atts( array(
			'field' => 'title',
			'posttype' => '',
			'filters' => "true"
		), $atts ) );

		$filters = $filters === 'true' ? true : false;
		
		$options = get_option($posttype.'_options');
		if($options){
			if(array_key_exists($field, $options)) {				
				if($field == "content" && $filters) {
					return apply_filters('the_content', $options[$field]);
				} else {
					return $options[$field];
				}	
			}
		}
		
	}
	
/* 	=============================
   	Other Functions 
   	============================= */
   	
   	function get_posttype_title($curr_PT = "") {
   	
   		$title = "";
   		if($curr_PT != "") {
	   		$PT_Obj = get_post_type_object($curr_PT);
	   		$title = $PT_Obj->labels->name;
   		}
   	
	   	return $title;
   	}

/* 	=============================
   	PHP 5 Constructor 
   	============================= */
   	
	function __construct() {
		
		add_action('admin_menu', array( &$this, 'create_subpages') );
		add_action('admin_init', array( &$this, 'register_settings') );
		add_action('jck_admin_notices', array( &$this, 'admin_msgs') );
		
		add_shortcode( 'post_type_intro', array( &$this, 'post_type_intro_func') );
	
	}
	
/* 	=============================
   	PHP 4 Compatible Constructor 
   	============================= */
   	
	function jck_cpti() {
		$this->__construct();
	}
  
} // End jck_cpti Class

$jck_cpti = new jck_cpti; // Start an instance of the plugin class