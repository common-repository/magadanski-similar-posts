<?php
/**
 * Plugin Name: M Similar Posts
 * Plugin URI: http://wordpress.org/plugins/magadanski-similar-posts/
 * Description: Shows similar posts ordered by the number of common categories.
 * Version: 1.3.4
 * Author: Georgi Popov a.k.a. Magadanski_Uchen
 * Author URI: http://magadanski.com/
 * License: GPL2
 * Text Domain: magadanski-similar-posts
 * Domain Path: /languages
 */

define('MSP_DIR', dirname(__FILE__) . DIRECTORY_SEPARATOR);

require_once(MSP_DIR . 'helper_functions.php');
require_once(MSP_DIR . 'Magadanski_Similar_Posts_Widget.php');

/**
 * Plugin Singleton Class
 * 
 * @since 1.0
 */
class Magadanski_Similar_Posts {
	/**
	 * Plugin version constant
	 * 
	 * @since 1.1
	 * @access public
	 * @const VERSION
	 */
	const VERSION = '1.3.4';
	
	/**
	 * Singleton instance holder
	 * 
	 * @since 1.0
	 * @access private
	 * @var Magadanski_Similar_Posts
	 */
	private static $instance = null;
	
	/**
	 * Query arguments store
	 * 
	 * @since 1.0
	 * @access private
	 * @var array
	 */
	private $args = array();
	
	/**
	 * Holder for current/query post ID
	 * 
	 * @since 1.0
	 * @access private
	 * @var int
	 */
	private $similar_id = 0;
	
	/**
	 * Plugin constructor
	 * 
	 * There should be only a single instance of the class. Therefor the constructor method is private.
	 * Use Magadanski_Similar_Posts::get_instance() to obtain access to the class' only instance.
	 * @since 1.0
	 * @access private
	 * @see Magadanski_Similar_Posts::get_instance()
	 * @return Magadanski_Similar_Posts
	 */
	private function __construct() {
		add_action('plugins_loaded', array(&$this, 'init'));
	}
	
	/**
	 * Class cloning is forbidden in order to keep just a single instance.
	 * 
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function __clone() {}
	
	/**
	 * Provides access to the class' instance
	 * 
	 * @since 1.0
	 * @access public
	 * @return Magadanski_Similar_Posts
	 */
	public static function get_instance() {
		if (!self::$instance instanceof self) {
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	/**
	 * Setter for the $similar_id property
	 * 
	 * @since 1.0
	 * @access private
	 * @param int $id the ID to be stored
	 * @return void
	 */
	private function set_similar_id($id) {
		$id = absint($id);
		
		if ($id) {
			$this->similar_id = $id;
		} else if (!$this->similar_id) {
			$this->similar_id = get_the_ID();
		}
	}
	
	/**
	 * Helper function to provide URL to the plugins directory
	 * 
	 * @since 1.1
	 * @access public
	 * @return void
	 */
	public function plugin_url() {
		return plugins_url('/', __FILE__);
	}
	
	/**
	 * Plugin initialization. Called on the plugins_loaded hook
	 * 
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function init() {
		load_plugin_textdomain('magadanski-similar-posts', false, basename(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'languages');
		
		add_action('widgets_init', array(&$this, 'widgets_init'));
		add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue_scripts'));
		
		add_shortcode('magadanski-similar-posts', array(&$this, 'shortcode'));
		
		$this->vc_integration();
		$this->gutenberg_integration();
	}
	
	/**
	 * Plugin widget initialization. Called on the widgets_init hook.
	 * 
	 * @since 1.0
	 * @access public
	 * @return void
	 */
	public function widgets_init() {
		register_widget('Magadanski_Similar_Posts_Widget');
	}
	
	/**
	 * Adding admin panel scripts and styles
	 * 
	 * @since 1.1
	 * @access public
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style('msp-admin', $this->plugin_url() . 'admin.css', array(), self::VERSION, 'all');
		wp_enqueue_style('msp-block', $this->plugin_url() . 'blocks/dist/edit.css', array(), self::VERSION, 'all');
	}
	
	/**
	 * Plugin default query parameters
	 * 
	 * @since 1.0
	 * @access public
	 * @return array The default options for the plugin when making a query
	 */
	public function get_defaults() {
		return array(
			'post_type' => 'post',
			'posts_per_page' => 5,
			'taxonomy' => 'category',
			'no_found_rows' => true
		);
	}
	
	/**
	 * Main plugin functionality method.
	 * 
	 * Easily called from other plugins and/or themes. Allows you to hook to the plugin's functionality.
	 * @since 1.0
	 * @access public
	 * @param array $args Query parameters that will later on be passed to WP_Query. The parameters can be compatible. Terms from the `taxonomy` parameter will be used to determine similarity
	 * @param int $similar_id = 0 The ID of the post you'd like to get similar entries for. By default this will use the current post's ID.
	 * @return WP_Query Object containing similar posts.
	 */
	public function get_similar_posts($args = array(), $similar_id = 0) {
		$this->set_similar_id($similar_id);
		
		$this->args = wp_parse_args($args, $this->get_defaults());
		
		// attach necessary filters
		$this->add_query_filters();
		$similar_posts = new WP_Query($this->args);
		
		// remove filters not to affect other queries
		$this->remove_query_filters();
		
		return $similar_posts;
	}
	
	/**
	 * Method to add custom query filters.
	 * 
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function add_query_filters() {
		add_filter('posts_fields_request', array(&$this, 'posts_fields_request'));
		add_filter('posts_join', array(&$this, 'posts_join'));
		add_filter('posts_where', array(&$this, 'posts_where'));
		add_filter('posts_groupby', array(&$this, 'posts_groupby'));
		add_filter('posts_orderby', array(&$this, 'posts_orderby'));
	}
	
	/**
	 * Method to remove custom query filters.
	 * 
	 * @since 1.0
	 * @access private
	 * @return void
	 */
	private function remove_query_filters() {
		remove_filter('posts_fields_request', array(&$this, 'posts_fields_request'));
		remove_filter('posts_join', array(&$this, 'posts_join'));
		remove_filter('posts_where', array(&$this, 'posts_where'));
		remove_filter('posts_groupby', array(&$this, 'posts_groupby'));
		remove_filter('posts_orderby', array(&$this, 'posts_orderby'));
	}
	
	/**
	 * Posts request modification filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_fields_request($fields) {
		global $wpdb;

    return "$fields, COUNT(`{$wpdb->posts}`.`post_title`) AS `msp_connections`";
	}
	
	/**
	 * Query join filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_join($join) {
		global $wpdb;
		
		$join .= "
			INNER JOIN `$wpdb->term_relationships` AS `msp_term_rel` ON (`msp_term_rel`.`object_id` = `$wpdb->posts`.`ID`)
			INNER JOIN `$wpdb->term_taxonomy` AS `msp_term_tax` ON (`msp_term_tax`.`term_taxonomy_id` = `msp_term_rel`.`term_taxonomy_id`) ";
		
		return $join;
	}
	
	/**
	 * Query where filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_where($where) {
		global $wpdb;
		
		$where .= "
			AND `msp_term_rel`.`term_taxonomy_id` IN (
				SELECT `term_rel`.`term_taxonomy_id`
				FROM `$wpdb->term_relationships` AS `term_rel`
				WHERE `term_rel`.`object_id` = {$this->similar_id}
			)
			AND `msp_term_tax`.`taxonomy` = '{$this->args['taxonomy']}'
			AND `$wpdb->posts`.`ID` != {$this->similar_id} ";
		
		return $where;
	}
	
	/**
	 * Query groupby filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_groupby($groupby) {
		global $wpdb;
		
		return " `$wpdb->posts`.`post_title` ";
	}
	
	/**
	 * Query orderby filter
	 * 
	 * @since 1.0
	 * @access public
	 * @return string
	 */
	public function posts_orderby($orderby) {
		return " `msp_connections` DESC ";
	}
	
	/**
	 * Plugin shortcode method
	 * 
	 * @since 1.0.3
	 * @access public
	 * @return string
	 */
	public function shortcode($atts) {
		$output = '';
		
		$current_entry_id = $_GET['post'] ?? ($_GET['post_id'] ?? 0);
		
		if ($current_entry_id) {
			$current_entry = get_post($current_entry_id);
		}
		
		extract(shortcode_atts(array(
			'id' => get_the_ID(),
			'post_type' => $current_entry->post_type ?? 'post',
			'taxonomy' => $_GET['attributes']['taxonomy'] ?? 'category',
			'limit' => 5,
		), $atts));
		
		$similar_posts = $this->get_similar_posts(array('post_type'=>$post_type, 'taxonomy'=>$taxonomy, 'posts_per_page'=>absint($limit), 'no_found_rows'=>true), $id);
		
		if ($similar_posts->have_posts()) {
			ob_start();
			
			$tag = apply_filters('msp_similar_posts_list_tag', 'ul');
			$tag = apply_filters('msp_similar_posts_shortcode_tag', $tag);
			
			$classes = apply_filters('msp_similar_posts_list_classes', array('msp-list'));
			$classes = apply_filters('msp_similar_posts_shortcode_classes', $classes);
			
			echo '<' . $tag . ' class="' . implode(' ', $classes) . '">';
			while ($similar_posts->have_posts()) {
				$similar_posts->the_post();
				$post_title = get_the_title();
				echo '<li><a href="' . get_permalink(get_the_ID()) . '" title="' . esc_attr($post_title) . '">' . $post_title . '</a></li>';
			}
			echo '</' . $tag . '>';
			
			$output = ob_get_clean();
		}
		
		wp_reset_postdata();
		
		return $output;
	}
	
	/**
	 * Visual Composer Integration
	 * 
	 * @since 1.1
	 * @access public
	 * @return void
	 */
	public function vc_integration() {
		if (function_exists('vc_map')) {
			$defaults = $this->get_defaults();
			
			$all_post_types = msp_get_post_types();
			$post_type_options = array();
			
			$all_taxonomies = msp_get_taxonomies($current_post_type);
			$taxonomy_options = array();
			
			foreach ($all_post_types as $post_type => $post_type_object) {
				$post_type_options[$post_type_object->labels->name] = $post_type;
			}
			
			foreach ($all_taxonomies as $tax => $tax_object) {
				$taxonomy_options[$tax_object->labels->name] = $tax;
			}
			
			vc_map(array(
				'name' => __('M Similar Posts', 'magadanski-similar-posts'),
				'base' => 'magadanski-similar-posts',
				'class' => '',
				'category' => __('Content', 'magadanski-similar-posts'),
				'icon' => 'msp',
				'params' => array(
					array(
						'type' => 'textfield',
						'heading' => __('Limit', 'magadanski-similar-posts'),
						'param_name' => 'limit',
						'value' => '5',
						'description' => __('Maximum number of similar items to display', 'magadanski-similar-posts'),
						'admin_label' => false,
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Post Type', 'magadanski-similar-posts'),
						'param_name' => 'post_type',
						'value' => $post_type_options,
						'description' => __('The type of post to show similar entries from', 'magadanski-similar-posts'),
						'admin_label' => true,
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Taxonomy', 'magadanski-similar-posts'),
						'param_name' => 'taxonomy',
						'value' => $taxonomy_options,
						'description' => __('Taxonomy the similarity should be based on', 'magadanski-similar-posts'),
						'admin_label' => true,
					),
				),
			));
		}
	}
	
	/**
	 * Gutenberg Integration
	 * 
	 * Register a Gutenberg Block for WordPress 5+
	 * 
	 * @since 1.3.0
	 * @access public
	 * @return void
	 */
	public function gutenberg_integration() {
		if (function_exists('register_block_type')) {
			$script_dependencies = array(
				'lodash',
				'wp-i18n',
				'wp-data',
				'wp-blocks',
				'wp-element',
				'wp-components',
				'wp-block-editor',
				'wp-server-side-render',
			);
			
			wp_register_script('msp-block', $this->plugin_url() . '/blocks/dist/index.js', $script_dependencies, self::VERSION);
			
			wp_set_script_translations( 'msp-block', 'magadanski-similar-posts', basename( __DIR__ ) . DIRECTORY_SEPARATOR . 'languages' );
			
			add_filter('load_script_translation_file', array(&$this, 'pass_proper_translation_file'), 10, 3);
			
			$defaults = $this->get_defaults();
			
			$block_options = array(
				'render_callback' => array(&$this, 'shortcode'),
				'editor_script' => 'msp-block',
				'attributes' => array(
					'limit' => array('type' => 'number', 'default' => $defaults['posts_per_page']),
					'taxonomy' => array('type' => 'string', 'default' => 'category'),
					'post_type' => array('type' => 'string', 'default' => 'post')
					
				),
			);
			
			register_block_type('magadanski/similar-posts', $block_options);
		}
	}
	
	/**
	 * Manually generate the path to the Gutenberg Block translation file
	 * 
	 * WordPress seems to have issues with OS compatibility and does not properly generate the path to translation files on Windows.
	 * This method makes sure that a proper path is provided, so the file is readable.
	 * 
	 * @since 1.3.2
	 * @access public
	 * @return string
	 */
	public function pass_proper_translation_file($file, $handle, $domain) {
		if ($handle === 'msp-block' && $domain === 'magadanski-similar-posts') {
			$file = MSP_DIR . 'languages' . DIRECTORY_SEPARATOR . 'magadanski-similar-posts-bg_BG-4ddeb242cd275f27b6d5ca36a93ac904.json';
		}
		
		return $file;
	}
}

global $magadanski_similar_posts;
$magadanski_similar_posts = Magadanski_Similar_Posts::get_instance();

// Shortcut function for Magadanski_Similar_Posts::get_similar_posts()
function msp_get_similar_posts($args = array(), $similar_id = 0) {
	global $magadanski_similar_posts;
	
	return $magadanski_similar_posts->get_similar_posts($args, $similar_id);
	
}

?>
