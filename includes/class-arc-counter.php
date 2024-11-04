<?php
/**
 * Main Counter Class
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ARC_Counter {
	/**
	 * Plugin settings
	 */
	private $settings;

	/**
	 * Database table name
	 */
	private $table_name;

	/**
	 * Flag to prevent duplicate insertion
	 */
	private $counter_added = false;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'arc_readers';
		$this->settings = get_option( 'arc_settings', array() );

		// Initialize with high priority to ensure proper loading
		add_action( 'wp', array( $this, 'init' ), 1 );
	}

	/**
	 * Initialize the counter
	 */
	public function init() {
		// Only proceed if we're on a single post/page
		if ( ! is_singular() ) {
			return;
		}

		// Add content filter for auto-insert
		if ( $this->get_setting( 'auto_insert' ) ) {
			add_filter( 'the_content', array( $this, 'maybe_auto_insert' ), 99999 );
		}

		// Track post view
		$this->track_post_view( get_post() );

		// Start session tracking
		$this->check_post_view();
	}

	/**
	 * Render the counter
	 */
	public function render( $atts = array() ) {
		// Get post ID
		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return '';
		}

		// Parse attributes
		$defaults = array(
			'before_text' => $this->get_setting( 'before_text', __( 'Currently reading:', 'article-readers-counter' ) ),
			'after_text' => $this->get_setting( 'after_text', __( 'readers', 'article-readers-counter' ) ),
			'class' => 'arc-reader-count',
			'theme' => $this->get_setting( 'theme', 'default' ),
			'show_zero' => true
		);

		$atts = wp_parse_args( $atts, $defaults );

		// Get current count
		$count = $this->get_current_count( $post_id );

		// Don't show if count is 0 and show_zero is false
		if ( ! $count && ! $atts['show_zero'] ) {
			return '';
		}

		// Build CSS classes
		$classes = array_filter( array(
			'arc-reader-count',
			$atts['class'],
			'theme-' . sanitize_html_class( $atts['theme'] ),
			is_rtl() ? 'rtl' : 'ltr'
		) );

		// Build HTML
		$html = sprintf(
			'<div><div class="%1$s" data-post-id="%2$d" data-refresh="%3$d">',
			esc_attr( implode( ' ', $classes ) ),
			$post_id,
			absint( $this->get_setting( 'refresh_interval', 15 ) )
		);

		if ( ! empty( $atts['before_text'] ) ) {
			$html .= sprintf(
				'<span class="arc-before-text">%s</span> ',
				esc_html( $atts['before_text'] )
			);
		}

		$html .= sprintf(
			'<span class="arc-count" aria-live="polite">%d</span>',
			$count
		);

		if ( ! empty( $atts['after_text'] ) ) {
			$html .= sprintf(
				' <span class="arc-after-text">%s</span>',
				esc_html( $atts['after_text'] )
			);
		}

		$html .= '</div></div>';

		return $html;
	}

	/**
	 * Get current reader count
	 */
	public function get_current_count( $post_id ) {
		global $wpdb;

		$cleanup_interval = $this->get_setting( 'cleanup_interval', 30 );

		$count = $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(DISTINCT user_ip) 
            FROM {$this->table_name} 
            WHERE post_id = %d 
            AND last_activity > DATE_SUB(%s, INTERVAL %d SECOND)",
			$post_id,
			current_time( 'mysql' ),
			$cleanup_interval
		) );

		return (int) $count;
	}


	/**
	 * Track post view
	 */
	public function track_post_view( $post ) {
		if ( ! $post instanceof WP_Post ) {
			return;
		}

		// Update reader status
		$this->update_reader_status( $post->ID );
	}

	/**
	 * Check post view status
	 */
	public function check_post_view() {
		if ( ! is_singular() ) {
			return;
		}

		$post_id = get_the_ID();
		if ( ! $post_id ) {
			return;
		}

		$this->maybe_clean_old_records();
		$this->update_reader_status( $post_id );
	}

	/**
	 * Auto insert counter
	 */
	public function maybe_auto_insert( $content ) {
		// Prevent multiple insertions
		if ( $this->counter_added ) {
			return $content;
		}

		// Only insert on main query and in the loop
		if ( ! is_main_query() || ! in_the_loop() ) {
			return $content;
		}

		// Mark as added
		$this->counter_added = true;

		// Add counter to content
		return $content . $this->render();
	}

	/**
	 * Update total views
	 */
	private function update_total_views( $post_id ) {
		$views = (int) get_post_meta( $post_id, '_arc_total_views', true );
		update_post_meta( $post_id, '_arc_total_views', ++$views );
	}

	/**
	 * Update reader status
	 */
	private function update_reader_status( $post_id ) {
		global $wpdb;

		$wpdb->replace(
			$this->table_name,
			array(
				'post_id' => $post_id,
				'client_id' => $this->get_client_id(),
				'user_ip' => $this->get_client_ip(),
				'last_activity' => current_time( 'mysql' )
			),
			array( '%d', '%s', '%s', '%s' )
		);
	}

	/**
	 * Clean old records if needed
	 */
	private function maybe_clean_old_records() {
		global $wpdb;

		// Check if cleanup is needed
		$last_cleanup = get_transient( 'arc_last_cleanup' );
		if ( false !== $last_cleanup ) {
			return;
		}

		// Set cleanup flag
		set_transient( 'arc_last_cleanup', time(), MINUTE_IN_SECONDS );

		// Clean old records
		$cleanup_interval = $this->get_setting( 'cleanup_interval', 30 );

		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$this->table_name} 
            WHERE last_activity < DATE_SUB(%s, INTERVAL %d SECOND)",
			current_time( 'mysql' ),
			$cleanup_interval
		) );
	}

	/**
	 * Render counter number
	 */
	private function render_counter( $count ) {
		return sprintf(
			'<span class="arc-count" aria-live="polite">%d</span>',
			$count
		);
	}

	/**
	 * Render before counter text
	 */
	private function before_counter( $atts ) {
		if ( empty( $atts['before_text'] ) ) {
			return '';
		}

		return sprintf(
			'<span class="arc-before-text">%s</span>',
			esc_html( $atts['before_text'] )
		);
	}

	/**
	 * Render after counter text
	 */
	private function after_counter( $atts ) {
		if ( empty( $atts['after_text'] ) ) {
			return '';
		}

		return sprintf(
			'<span class="arc-after-text">%s</span>',
			esc_html( $atts['after_text'] )
		);
	}

	/**
	 * Get client ID
	 */
	private function get_client_id() {
		if ( isset( $_COOKIE['arc_client_id'] ) ) {
			return sanitize_text_field( $_COOKIE['arc_client_id'] );
		}

		$client_id = md5( uniqid() . $this->get_client_ip() );
		setcookie( 'arc_client_id', $client_id, time() + DAY_IN_SECONDS, COOKIEPATH, COOKIE_DOMAIN );

		return $client_id;
	}

	/**
	 * Get client IP
	 */
	private function get_client_ip() {
		$ip = '';

		if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		} elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '0.0.0.0';
	}

	/**
	 * Get setting with default
	 */
	private function get_setting( $key, $default = '' ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : $default;
	}

	/**
	 * Get analytics data
	 */
	public function get_analytics( $post_id, $period = '24h' ) {
		global $wpdb;

		$data = [ 
			'current' => $this->get_current_count( $post_id ),
			'total_views' => get_post_meta( $post_id, '_arc_total_views', true ),
			'peak_readers' => 0,
			'average_readers' => 0,
			'peak_time' => null
		];

		// Additional analytics based on period
		switch ( $period ) {
			case '24h':
				$interval = '24 HOUR';
				break;
			case '7d':
				$interval = '7 DAY';
				break;
			case '30d':
				$interval = '30 DAY';
				break;
			default:
				$interval = '24 HOUR';
		}

		// Get peak readers
		$peak = $wpdb->get_row( $wpdb->prepare(
			"SELECT COUNT(DISTINCT user_ip) as count, DATE_FORMAT(last_activity, '%Y-%m-%d %H:00:00') as hour
            FROM {$this->table_name}
            WHERE post_id = %d
            AND last_activity > DATE_SUB(%s, INTERVAL {$interval})
            GROUP BY hour
            ORDER BY count DESC
            LIMIT 1",
			$post_id,
			current_time( 'mysql' )
		) );

		if ( $peak ) {
			$data['peak_readers'] = (int) $peak->count;
			$data['peak_time'] = $peak->hour;
		}

		return apply_filters( 'arc_analytics_data', $data, $post_id, $period );
	}
}