(function ($) {
	"use strict";

	class ReaderCounter {
		constructor() {
			// Initialize properties
			this.container = $(".arc-reader-count");
			this.countElement = this.container.find(".arc-count");
			this.postId = this.container.data("post-id");
			this.refreshInterval = arcSettings.refreshInterval || 15000;
			this.retryTimeout = 5000;
			this.maxRetries = 3;
			this.retryCount = 0;
			this.updateTimer = null;
			this.active = true;

			// Initialize if container exists
			if (this.container.length) {
				this.init();
			}
		}

		/**
		 * Initialize the counter
		 */
		init() {
			// Initial update
			this.updateCount();

			// Bind events
			this.bindEvents();

			// Start auto-refresh
			this.startAutoRefresh();
		}

		/**
		 * Bind all necessary events
		 */
		bindEvents() {
			// Handle visibility change
			document.addEventListener("visibilitychange", () => {
				if (document.hidden) {
					this.pause();
				} else {
					this.resume();
				}
			});

			// Handle page unload
			window.addEventListener("beforeunload", () => {
				this.disconnect();
			});

			// Handle errors
			this.container.on("arc:error", (e, error) => {
				this.handleError(error);
			});
		}

		/**
		 * Start auto-refresh timer
		 */
		startAutoRefresh() {
			this.updateTimer = setInterval(() => {
				if (this.active) {
					this.updateCount();
				}
			}, this.refreshInterval);
		}

		/**
		 * Pause counter updates
		 */
		pause() {
			this.active = false;
			this.updateStatus("inactive");
			clearInterval(this.updateTimer);
		}

		/**
		 * Resume counter updates
		 */
		resume() {
			this.active = true;
			this.updateCount();
			this.startAutoRefresh();
		}

		/**
		 * Update reader count
		 */
		updateCount() {
			if (!this.active) return;

			this.container.addClass("loading");

			$.ajax({
				url: arcSettings.ajaxUrl,
				type: "POST",
				data: {
					action: "arc_update_count",
					action_type: "update",
					post_id: this.postId,
					nonce: arcSettings.nonce,
				},
				success: (response) => {
					this.container.removeClass("loading");

					if (response.success) {
						this.retryCount = 0;
						this.updateCounter(response.data.count);
					} else {
						this.handleError(response.data.message);
					}
				},
				error: (xhr, status, error) => {
					this.handleError(error);
				},
			});
		}

		/**
		 * Update the counter display
		 */
		updateCounter(count) {
			const currentCount = parseInt(this.countElement.text());
			const newCount = parseInt(count);

			if (currentCount !== newCount) {
				// Add update animation class
				this.countElement.addClass("arc-count-updating");

				// Update the number
				this.countElement.text(newCount);

				// Remove animation class after animation completes
				setTimeout(() => {
					this.countElement.removeClass("arc-count-updating");
				}, 300);
			}
		}

		/**
		 * Handle errors
		 */
		handleError(error) {
			console.error("Reader Counter Error:", error);
			this.container.removeClass("loading").addClass("error");

			// Retry logic
			if (this.retryCount < this.maxRetries) {
				this.retryCount++;
				setTimeout(() => {
					this.updateCount();
				}, this.retryTimeout);
			} else {
				this.container.trigger("arc:maxRetriesReached");
			}
		}

		/**
		 * Update reader status
		 */
		updateStatus(status) {
			$.ajax({
				url: arcSettings.ajaxUrl,
				type: "POST",
				data: {
					action: "arc_update_count",
					action_type: "status",
					post_id: this.postId,
					status: status,
					nonce: arcSettings.nonce,
				},
			});
		}

		/**
		 * Disconnect reader
		 */
		disconnect() {
			$.ajax({
				url: arcSettings.ajaxUrl,
				type: "POST",
				async: false, // Important for beforeunload
				data: {
					action: "arc_update_count",
					action_type: "disconnect",
					post_id: this.postId,
					nonce: arcSettings.nonce,
				},
			});
		}

		/**
		 * Clean up
		 */
		destroy() {
			clearInterval(this.updateTimer);
			this.container.off("arc:error");
			this.active = false;
		}
	}

	// Initialize on document ready
	$(document).ready(() => {
		// Create new instance
		const counter = new ReaderCounter();

		// Store instance in data for external access
		if (counter.container.length) {
			counter.container.data("arcCounter", counter);
		}
	});

	// Add to global scope for external access
	window.ArcReaderCounter = ReaderCounter;
})(jQuery);
