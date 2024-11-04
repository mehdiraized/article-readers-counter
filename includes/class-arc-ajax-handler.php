<?php
/**
 * AJAX Handler for Article Readers Counter
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ARC_Ajax_Handler {
	/**
	 * Database table name
	 */
	private $table_name;

	/**
	 * Settings array
	 */
	private $settings;

	/**
	 * Constructor
	 */
	public function __construct() {
		global $wpdb;
		$this->table_name = $wpdb->prefix . 'arc_readers';
		$this->settings = get_option( 'arc_settings' );
	}

	/**
	 * Update reader count
	 */
	public function update_count() {
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid post ID', 'article-readers-counter' ) ] );
		}

		try {
			// Clean old records first
			$this->clean_old_records();

			// Get client info
			$client_id = $this->get_client_id();
			$user_ip = $this->get_client_ip();

			// Update or insert reader record
			$this->update_reader_record( $post_id, $client_id, $user_ip );

			// Get updated count
			$count = $this->get_reader_count( $post_id );

			// Update post meta
			update_post_meta( $post_id, '_arc_reader_count', $count );

			wp_send_json_success( [ 
				'count' => $count,
				'timestamp' => current_time( 'timestamp' )
			] );

		} catch (Exception $e) {
			wp_send_json_error( [ 
				'message' => $e->getMessage()
			] );
		}
	}

	/**
	 * Get current reader count
	 */
	public function get_count() {
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;

		if ( ! $post_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid post ID', 'article-readers-counter' ) ] );
		}

		try {
			$count = $this->get_reader_count( $post_id );

			wp_send_json_success( [ 
				'count' => $count,
				'timestamp' => current_time( 'timestamp' )
			] );

		} catch (Exception $e) {
			wp_send_json_error( [ 
				'message' => $e->getMessage()
			] );
		}
	}

	/**
	 * Handle disconnect
	 */
	public function handle_disconnect() {
		$post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
		$client_id = isset( $_POST['client_id'] ) ? sanitize_text_field( $_POST['client_id'] ) : '';

		if ( ! $post_id || ! $client_id ) {
			wp_send_json_error( [ 'message' => __( 'Invalid request data', 'article-readers-counter' ) ] );
		}

		try {
			global $wpdb;

			// Remove reader record
			$wpdb->delete(
				$this->table_name,
				[ 
					'post_id' => $post_id,
					'client_id' => $client_id
				],
				[ '%d', '%s' ]
			);

			// Get updated count
			$count = $this->get_reader_count( $post_id );

			// Update post meta
			update_post_meta( $post_id, '_arc_reader_count', $count );

			wp_send_json_success( [ 
				'count' => $count,
				'timestamp' => current_time( 'timestamp' )
			] );

		} catch (Exception $e) {
			wp_send_json_error( [ 
				'message' => $e->getMessage()
			] );
		}
	}

	/**
	 * Update reader record
	 */
	private function update_reader_record( $post_id, $client_id, $user_ip ) {
		global $wpdb;

		$result = $wpdb->replace(
			$this->table_name,
			[ 
				'post_id' => $post_id,
				'client_id' => $client_id,
				'user_ip' => $user_ip,
				'last_activity' => current_time( 'mysql' )
			],
			[ '%d', '%s', '%s', '%s' ]
		);

		if ( false === $result ) {
			throw new Exception( __( 'Failed to update reader record', 'article-readers-counter' ) );
		}
	}

	/**
	 * Get reader count for a post
	 */
	private function get_reader_count( $post_id ) {
		global $wpdb;

		return (int) $wpdb->get_var( $wpdb->prepare(
			"SELECT COUNT(DISTINCT user_ip) FROM {$this->table_name} 
            WHERE post_id = %d AND last_activity > DATE_SUB(%s, INTERVAL %d SECOND)",
			$post_id,
			current_time( 'mysql' ),
			$this->settings['cleanup_interval']
		) );
	}

	/**
	 * Clean old records
	 */
	private function clean_old_records() {
		global $wpdb;

		$wpdb->query( $wpdb->prepare(
			"DELETE FROM {$this->table_name} 
            WHERE last_activity < DATE_SUB(%s, INTERVAL %d SECOND)",
			current_time( 'mysql' ),
			$this->settings['cleanup_interval']
		) );
	}

	/**
	 * Generate or get client ID
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
	 * Get client IP address
	 */
	private function get_client_ip() {
		$ip = '';

		// CloudFlare IP
		if ( isset( $_SERVER['HTTP_CF_CONNECTING_IP'] ) ) {
			$ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
		}
		// Regular IP sources
		elseif ( isset( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = trim( current( explode( ',', $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) );
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		// Validate IP
		if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
			return sanitize_text_field( $ip );
		}

		// Fallback
		return '0.0.0.0';
	}

	/**
	 * Log debug information
	 */
	private function log( $message ) {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( "[ARC Debug] " . print_r( $message, true ) );
		}
	}

	/**
	 * Validate nonce
	 */
	private function validate_nonce() {
		if ( ! check_ajax_referer( 'arc-counter', 'nonce', false ) ) {
			wp_send_json_error( [ 
				'message' => __( 'Security check failed', 'article-readers-counter' )
			] );
		}
	}
}