<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<?php settings_errors( 'arc_settings' ); ?>

	<div class="arc-admin-container">
		<!-- Main Settings Form -->
		<div class="arc-settings-form">
			<form method="post" action="options.php">
				<?php
				settings_fields( 'arc_settings' );
				do_settings_sections( 'arc-settings' );
				submit_button();
				?>
			</form>

			<div class="arc-info-card">
				<h3><?php esc_html_e( 'Support Us', 'article-readers-counter' ); ?></h3>
				<p>
					<?php esc_html_e( 'Thank you for using Article Readers Counter! This plugin is a labor of love, designed to help you to show real-time reader count for posts. If you find it useful, please consider supporting its development.', 'article-readers-counter' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'Your support helps cover the costs of maintaining and improving the plugin, ensuring it remains free and accessible for everyone. Every little bit helps and is greatly appreciated!', 'article-readers-counter' ); ?>
				</p>
				<a href="https://www.buymeacoffee.com/mehdiraized" target="_blank">
					<img src="<?php echo esc_url( plugins_url( '../assets/img/bmc-button.png', __FILE__ ) ); ?>"
						alt="Buy Me A Coffee" style="height: 60px !important;width: 217px !important;">
				</a>
				<p><?php esc_html_e( 'Thank you for your generosity and support!', 'article-readers-counter' ); ?></p>
			</div>
		</div>

		<!-- Sidebar -->
		<div class="arc-settings-sidebar">
			<!-- Shortcode Info -->
			<div class="arc-info-card">
				<h3><?php _e( 'Shortcode Usage', 'article-readers-counter' ); ?></h3>
				<div class="arc-info-content">
					<p><?php _e( 'Use this shortcode to display the counter:', 'article-readers-counter' ); ?></p>
					<code>[readers_count]</code>

					<p><?php _e( 'With custom parameters:', 'article-readers-counter' ); ?></p>
					<code>[readers_count before_text="Reading now: " after_text=" visitors" theme="boxed"]</code>

					<h4><?php _e( 'Available Parameters:', 'article-readers-counter' ); ?></h4>
					<ul>
						<li><code>before_text</code> -
							<?php _e( 'Text before the counter', 'article-readers-counter' ); ?>
						</li>
						<li><code>after_text</code> - <?php _e( 'Text after the counter', 'article-readers-counter' ); ?>
						</li>
						<li><code>theme</code> -
							<?php _e( 'Counter theme (default, minimal, boxed, rounded, accent)', 'article-readers-counter' ); ?>
						</li>
						<li><code>class</code> - <?php _e( 'Custom CSS class', 'article-readers-counter' ); ?></li>
					</ul>
				</div>
			</div>

			<!-- Preview -->
			<div class="arc-info-card">
				<h3><?php _e( 'Live Preview', 'article-readers-counter' ); ?></h3>
				<div class="arc-preview-content">
					<div class="arc-preview-box">
						<?php
						// Get current settings
						$options = get_option( 'arc_settings' );
						$theme = isset( $options['theme'] ) ? $options['theme'] : 'default';
						$before_text = isset( $options['before_text'] ) ? $options['before_text'] : __( 'Currently reading:', 'article-readers-counter' );
						$after_text = isset( $options['after_text'] ) ? $options['after_text'] : __( 'readers', 'article-readers-counter' );
						?>
						<div class="arc-reader-count theme-<?php echo esc_attr( $theme ); ?>">
							<span class="arc-before-text"><?php echo esc_html( $before_text ); ?></span>
							<span class="arc-count">5</span>
							<span class="arc-after-text"><?php echo esc_html( $after_text ); ?></span>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>

<style>
	/* Admin Settings Styles */
	.arc-admin-container {
		display: flex;
		gap: 20px;
		margin-top: 20px;
	}

	.arc-settings-form {
		flex: 1;
	}

	.arc-settings-sidebar {
		width: 300px;
	}

	.arc-info-card {
		background: #fff;
		border: 1px solid #ccd0d4;
		border-radius: 4px;
		margin-bottom: 20px;
		padding: 15px;
	}

	.arc-info-card h3 {
		margin-top: 0;
		padding-bottom: 10px;
		border-bottom: 1px solid #eee;
	}

	.arc-info-content {
		color: #666;
	}

	.arc-info-content code {
		display: block;
		padding: 10px;
		margin: 10px 0;
		background: #f5f5f5;
		border: 1px solid #e5e5e5;
	}

	.arc-info-content ul {
		list-style: disc;
		margin-left: 20px;
	}

	.arc-preview-content {
		padding: 20px;
		background: #f9f9f9;
		border: 1px solid #e5e5e5;
		border-radius: 3px;
	}

	/* Theme Preview Styles */
	.arc-preview-box .arc-reader-count {
		display: inline-flex;
		align-items: center;
		padding: 10px 15px;
		margin: 0;
		border-radius: 4px;
		font-size: 14px;
		line-height: 1.4;
	}

	.arc-preview-box .arc-count {
		font-weight: bold;
		margin: 0 5px;
	}

	/* Theme Variations */
	.arc-preview-box .theme-default {
		background-color: #f8f9fa;
		border: 1px solid #dee2e6;
	}

	.arc-preview-box .theme-minimal {
		background: none;
		border: none;
		padding: 0;
	}

	.arc-preview-box .theme-boxed {
		background: #fff;
		border: 1px solid #e5e5e5;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	}

	.arc-preview-box .theme-rounded {
		border-radius: 25px;
		background: #f8f9fa;
		border: 1px solid #dee2e6;
	}

	.arc-preview-box .theme-accent {
		background: #007bff;
		color: #fff;
		border: none;
	}

	.arc-preview-box .theme-accent .arc-count {
		color: #fff;
	}

	/* Responsive Design */
	@media screen and (max-width: 782px) {
		.arc-admin-container {
			flex-direction: column;
		}

		.arc-settings-sidebar {
			width: 100%;
		}
	}
</style>

<script>
	jQuery(document).ready(function ($) {
		// Live preview updates
		function updatePreview() {
			var theme = $('#theme').val();
			var beforeText = $('#before_text').val() || '<?php echo esc_js( __( 'Currently reading:', 'article-readers-counter' ) ); ?>';
			var afterText = $('#after_text').val() || '<?php echo esc_js( __( 'readers', 'article-readers-counter' ) ); ?>';

			$('.arc-preview-box .arc-reader-count')
				.attr('class', 'arc-reader-count theme-' + theme)
				.find('.arc-before-text').text(beforeText).end()
				.find('.arc-after-text').text(afterText);
		}

		// Bind change events
		$('#theme, #before_text, #after_text').on('change keyup', updatePreview);

		// Initialize tooltips
		$('.arc-help-tip').tooltip();
	});
</script>