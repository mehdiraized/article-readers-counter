<?php
/**
 * Plugin Settings Class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ARC_Settings {
	/**
	 * Settings options
	 */
	private $options;

	/**
	 * Settings sections
	 */
	private $sections;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init_hooks();
		$this->options = get_option( 'arc_settings' );
	}

	/**
	 * Initialize hooks
	 */
	private function init_hooks() {
		add_action( 'admin_menu', array( $this, 'add_settings_page' ) );
		add_action( 'admin_init', array( $this, 'init_settings' ) );
		add_filter( 'plugin_action_links_' . ARC_PLUGIN_BASENAME, array( $this, 'add_settings_link' ) );
	}

	/**
	 * Add settings page
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Article Readers Counter Settings', 'article-readers-counter' ),
			__( 'Article Readers', 'article-readers-counter' ),
			'manage_options',
			'arc-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Initialize settings
	 */
	public function init_settings() {
		register_setting(
			'arc_settings',
			'arc_settings',
			array( $this, 'sanitize_settings' )
		);

		// Add settings sections
		$this->add_settings_sections();

		// Add settings fields
		$this->add_settings_fields();
	}

	/**
	 * Add settings sections
	 */
	private function add_settings_sections() {
		$this->sections = array(
			'general' => array(
				'id' => 'arc_general',
				'title' => __( 'General Settings', 'article-readers-counter' ),
				'callback' => array( $this, 'render_section_general' )
			),
			'display' => array(
				'id' => 'arc_display',
				'title' => __( 'Display Settings', 'article-readers-counter' ),
				'callback' => array( $this, 'render_section_display' )
			),
			'advanced' => array(
				'id' => 'arc_advanced',
				'title' => __( 'Advanced Settings', 'article-readers-counter' ),
				'callback' => array( $this, 'render_section_advanced' )
			)
		);

		foreach ( $this->sections as $section ) {
			add_settings_section(
				$section['id'],
				$section['title'],
				$section['callback'],
				'arc-settings'
			);
		}
	}

	/**
	 * Add settings fields
	 */
	private function add_settings_fields() {
		// General Settings
		add_settings_field(
			'auto_insert',
			__( 'Auto Insert', 'article-readers-counter' ),
			array( $this, 'render_checkbox_field' ),
			'arc-settings',
			'arc_general',
			array(
				'id' => 'auto_insert',
				'description' => __( 'Automatically add counter to all posts', 'article-readers-counter' )
			)
		);

		add_settings_field(
			'track_total_views',
			__( 'Track Total Views', 'article-readers-counter' ),
			array( $this, 'render_checkbox_field' ),
			'arc-settings',
			'arc_general',
			array(
				'id' => 'track_total_views',
				'description' => __( 'Track and store total view count for each post', 'article-readers-counter' )
			)
		);

		// Display Settings
		add_settings_field(
			'before_text',
			__( 'Before Text', 'article-readers-counter' ),
			array( $this, 'render_text_field' ),
			'arc-settings',
			'arc_display',
			array(
				'id' => 'before_text',
				'description' => __( 'Text to show before the counter', 'article-readers-counter' ),
				'placeholder' => __( 'Currently reading:', 'article-readers-counter' )
			)
		);

		add_settings_field(
			'after_text',
			__( 'After Text', 'article-readers-counter' ),
			array( $this, 'render_text_field' ),
			'arc-settings',
			'arc_display',
			array(
				'id' => 'after_text',
				'description' => __( 'Text to show after the counter', 'article-readers-counter' ),
				'placeholder' => __( 'readers', 'article-readers-counter' )
			)
		);

		add_settings_field(
			'theme',
			__( 'Counter Theme', 'article-readers-counter' ),
			array( $this, 'render_select_field' ),
			'arc-settings',
			'arc_display',
			array(
				'id' => 'theme',
				'description' => __( 'Select the display theme for the counter', 'article-readers-counter' ),
				'options' => array(
					'default' => __( 'Default', 'article-readers-counter' ),
					'minimal' => __( 'Minimal', 'article-readers-counter' ),
					'boxed' => __( 'Boxed', 'article-readers-counter' ),
					'rounded' => __( 'Rounded', 'article-readers-counter' ),
					'accent' => __( 'Accent', 'article-readers-counter' )
				)
			)
		);

		// Advanced Settings
		add_settings_field(
			'refresh_interval',
			__( 'Refresh Interval', 'article-readers-counter' ),
			array( $this, 'render_number_field' ),
			'arc-settings',
			'arc_advanced',
			array(
				'id' => 'refresh_interval',
				'description' => __( 'Interval in seconds to refresh the counter (min: 5, max: 60)', 'article-readers-counter' ),
				'min' => 5,
				'max' => 60,
				'step' => 5
			)
		);

		add_settings_field(
			'cleanup_interval',
			__( 'Cleanup Interval', 'article-readers-counter' ),
			array( $this, 'render_number_field' ),
			'arc-settings',
			'arc_advanced',
			array(
				'id' => 'cleanup_interval',
				'description' => __( 'Interval in seconds to clean up old records (min: 30, max: 300)', 'article-readers-counter' ),
				'min' => 30,
				'max' => 300,
				'step' => 30
			)
		);
	}

	/**
	 * Render settings page
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		require_once ARC_PLUGIN_DIR . 'templates/admin-settings.php';
	}

	/**
	 * Render section descriptions
	 */
	public function render_section_general() {
		echo '<p>' . esc_html__( 'Configure general plugin settings.', 'article-readers-counter' ) . '</p>';
	}

	public function render_section_display() {
		echo '<p>' . esc_html__( 'Customize how the counter appears on your site.', 'article-readers-counter' ) . '</p>';
	}

	public function render_section_advanced() {
		echo '<p>' . esc_html__( 'Advanced settings for performance and cleanup.', 'article-readers-counter' ) . '</p>';
	}

	/**
	 * Render form fields
	 */
	public function render_text_field( $args ) {
		$id = $args['id'];
		$value = isset( $this->options[ $id ] ) ? $this->options[ $id ] : '';
		$placeholder = isset( $args['placeholder'] ) ? $args['placeholder'] : '';

		printf(
			'<input type="text" id="%1$s" name="arc_settings[%1$s]" value="%2$s" class="regular-text" placeholder="%3$s" />',
			esc_attr( $id ),
			esc_attr( $value ),
			esc_attr( $placeholder )
		);

		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}

	public function render_number_field( $args ) {
		$id = $args['id'];
		$value = isset( $this->options[ $id ] ) ? $this->options[ $id ] : '';
		$min = isset( $args['min'] ) ? $args['min'] : 0;
		$max = isset( $args['max'] ) ? $args['max'] : 999;
		$step = isset( $args['step'] ) ? $args['step'] : 1;

		printf(
			'<input type="number" id="%1$s" name="arc_settings[%1$s]" value="%2$s" class="small-text" min="%3$d" max="%4$d" step="%5$d" />',
			esc_attr( $id ),
			esc_attr( $value ),
			$min,
			$max,
			$step
		);

		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}

	public function render_checkbox_field( $args ) {
		$id = $args['id'];
		$checked = isset( $this->options[ $id ] ) ? $this->options[ $id ] : 0;

		printf(
			'<label><input type="checkbox" id="%1$s" name="arc_settings[%1$s]" value="1" %2$s /> %3$s</label>',
			esc_attr( $id ),
			checked( $checked, 1, false ),
			isset( $args['description'] ) ? esc_html( $args['description'] ) : ''
		);
	}

	public function render_select_field( $args ) {
		$id = $args['id'];
		$value = isset( $this->options[ $id ] ) ? $this->options[ $id ] : '';
		$options = isset( $args['options'] ) ? $args['options'] : array();

		printf( '<select id="%1$s" name="arc_settings[%1$s]">', esc_attr( $id ) );

		foreach ( $options as $key => $label ) {
			printf(
				'<option value="%1$s" %2$s>%3$s</option>',
				esc_attr( $key ),
				selected( $value, $key, false ),
				esc_html( $label )
			);
		}

		echo '</select>';

		if ( isset( $args['description'] ) ) {
			printf( '<p class="description">%s</p>', esc_html( $args['description'] ) );
		}
	}

	/**
	 * Sanitize settings
	 */
	public function sanitize_settings( $input ) {
		if ( ! is_array( $input ) ) {
			return array();
		}

		$sanitized = array();

		// Checkbox fields
		$checkbox_fields = array( 'auto_insert', 'track_total_views' );
		foreach ( $checkbox_fields as $field ) {
			$sanitized[ $field ] = ! empty( $input[ $field ] ) ? 1 : 0;
		}

		// Text fields
		$text_fields = array( 'before_text', 'after_text', 'theme' );
		foreach ( $text_fields as $field ) {
			if ( isset( $input[ $field ] ) ) {
				$sanitized[ $field ] = sanitize_text_field( $input[ $field ] );
			}
		}

		// Number fields with min/max validation
		if ( isset( $input['refresh_interval'] ) ) {
			$sanitized['refresh_interval'] = min( max( absint( $input['refresh_interval'] ), 5 ), 60 );
		}

		if ( isset( $input['cleanup_interval'] ) ) {
			$sanitized['cleanup_interval'] = min( max( absint( $input['cleanup_interval'] ), 30 ), 300 );
		}

		return $sanitized;
	}

	/**
	 * Add settings link to plugins page
	 */
	public function add_settings_link( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'options-general.php?page=arc-settings' ),
			__( 'Settings', 'article-readers-counter' )
		);

		array_unshift( $links, $settings_link );
		return $links;
	}
}