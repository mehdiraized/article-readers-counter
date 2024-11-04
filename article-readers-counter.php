<?php
/**
 * Plugin Name: Article Readers Counter
 * Plugin URI: https://github.com/mehdiraized/article-readers-counter
 * Description: Display real-time reader count for articles
 * Short Description: Display real-time reader count for articles
 * Version: 1.0.1
 * Author: Mehdi Rezaei
 * Author URI: https://mehd.ir
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: article-readers-counter
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants
define( 'ARC_VERSION', '1.0.1' );
define( 'ARC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ARC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'ARC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

class Article_Readers_Counter {
	/**
	 * Single instance of the class
	 */
	private static $instance = null;

	/**
	 * Counter instance
	 */
	private $counter = null;

	/**
	 * Main plugin instance
	 */
	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor
	 */
	private function __construct() {
		$this->init_hooks();
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		// Load plugin dependencies
		add_action( 'plugins_loaded', array( $this, 'load_dependencies' ) );

		// Load textdomain
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );

		// Initialize counter after theme setup
		add_action( 'after_setup_theme', array( $this, 'init_counter' ) );

		// Register activation/deactivation hooks
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Endpoints for AJAX
		add_action( 'wp_ajax_arc_update_count', array( $this, 'handle_ajax' ) );
		add_action( 'wp_ajax_nopriv_arc_update_count', array( $this, 'handle_ajax' ) );

		// Frontend scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Register shortcode
		add_shortcode( 'readers_count', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Load plugin dependencies
	 */
	public function init_counter() {
		require_once ARC_PLUGIN_DIR . 'includes/class-arc-counter.php';
		$this->counter = new ARC_Counter();
	}

	/**
	 * Load plugin dependencies
	 */
	public function load_dependencies() {
		require_once ARC_PLUGIN_DIR . 'includes/class-arc-settings.php';
		require_once ARC_PLUGIN_DIR . 'includes/class-arc-ajax-handler.php';

		// Initialize settings
		new ARC_Settings();
	}

	/**
	 * Load plugin textdomain
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'article-readers-counter',
			false,
			dirname( ARC_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Plugin activation
	 */
	public function activate() {
		// Create database table
		global $wpdb;
		$table_name = $wpdb->prefix . 'arc_readers';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            user_ip varchar(100) NOT NULL,
            client_id varchar(32) NOT NULL,
            last_activity datetime NOT NULL,
            PRIMARY KEY  (id),
            KEY post_id (post_id),
            KEY last_activity (last_activity)
        ) $charset_collate;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );

		// Set default options
		$default_options = array(
			'auto_insert' => 0,
			'before_text' => __( 'Currently reading:', 'article-readers-counter' ),
			'after_text' => __( 'readers', 'article-readers-counter' ),
			'refresh_interval' => 15,
			'cleanup_interval' => 30,
		);
		add_option( 'arc_settings', $default_options );

		// Clear rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Plugin deactivation
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Enqueue frontend scripts and styles
	 */
	public function enqueue_scripts() {
		if ( ! is_single() ) {
			return;
		}

		wp_enqueue_style(
			'arc-styles',
			ARC_PLUGIN_URL . 'assets/css/style.css',
			array(),
			ARC_VERSION
		);

		wp_enqueue_script(
			'arc-counter',
			ARC_PLUGIN_URL . 'assets/js/reader-counter.js',
			array( 'jquery' ),
			ARC_VERSION,
			true
		);

		wp_localize_script( 'arc-counter', 'arcSettings', array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'postId' => get_the_ID(),
			'nonce' => wp_create_nonce( 'arc-counter' ),
			'refreshInterval' => $this->get_option( 'refresh_interval' ) * 1000,
			'i18n' => array(
				'error' => __( 'Error updating counter', 'article-readers-counter' )
			)
		) );
	}

	/**
	 * Handle AJAX requests
	 */
	public function handle_ajax() {
		check_ajax_referer( 'arc-counter', 'nonce' );

		$handler = new ARC_Ajax_Handler();
		$action = isset( $_POST['action_type'] ) ? sanitize_text_field( $_POST['action_type'] ) : '';

		switch ( $action ) {
			case 'update':
				$handler->update_count();
				break;
			case 'get':
				$handler->get_count();
				break;
			default:
				wp_send_json_error( 'Invalid action' );
		}
	}

	/**
	 * Render shortcode
	 */
	public function render_shortcode( $atts ) {
		$counter = new ARC_Counter();
		return $counter->render( $atts );
	}

	/**
	 * Get plugin option
	 */
	public function get_option( $key ) {
		$options = get_option( 'arc_settings' );
		return isset( $options[ $key ] ) ? $options[ $key ] : null;
	}
}

/**
 * Returns the main instance of Article_Readers_Counter
 */
function ARC() {
	return Article_Readers_Counter::instance();
}

// Initialize the plugin
ARC();