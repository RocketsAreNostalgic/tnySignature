/**
 * Improved Media uploader for the signature image
 *
 * Uses WordPress media API more effectively with better practices
 *
 * @param {Object} $ jQuery object
 * @since 0.3.3
 * @package
 */

/* global jQuery, TNYSINGNATURE */

(function ($) {
	'use strict';

	// Main media uploader object
	const SignatureMediaUploader = {
		// Store the media frame reference
		frame: null,

		// Cache DOM elements
		elements: {
			uploadButton: null,
			removeButton: null,
			imagePreview: null,
			imageIdInput: null,
			form: null,
		},

		// Settings that can be overridden
		settings: {
			defaultImage: '',
			title: 'Select or Upload Signature Image',
			buttonText: 'Use this image',
			multiple: false,
		},

		/**
		 * Initialize the media uploader
		 * @param {Object} options Configuration options
		 */
		init(options) {
			// Merge options with defaults
			this.settings = $.extend(this.settings, options || {});

			// Cache DOM elements
			this.elements.uploadButton = $('#uploadimage');
			this.elements.removeButton = $('#removeimage');
			this.elements.imagePreview = $('#signature-image-preview');
			this.elements.imageIdInput = $('#user_signature_image_id');
			this.elements.form = this.elements.imageIdInput.closest('form');

			// Set initial button state
			this.updateButtonState();

			// Bind events
			this.bindEvents();
		},

		/**
		 * Bind all event handlers
		 */
		bindEvents() {
			// Upload button click
			this.elements.uploadButton.on(
				'click',
				this.openMediaUploader.bind(this)
			);

			// Remove button click
			this.elements.removeButton.on('click', this.removeImage.bind(this));
		},

		/**
		 * Update button states based on whether an image is selected
		 */
		updateButtonState() {
			const hasImage = !!this.elements.imageIdInput.val();
			this.elements.removeButton.prop('disabled', !hasImage);
		},

		/**
		 * Open the WordPress media uploader
		 * @param {Event} e Click event
		 */
		openMediaUploader(e) {
			e.preventDefault();

			// If the frame already exists, reopen it
			if (this.frame) {
				this.frame.open();
				return;
			}

			// Create the media frame using WordPress media API
			this.frame = wp.media({
				title: this.settings.title,
				button: {
					text: this.settings.buttonText,
				},
				library: {
					type: 'image', // Limit to images only
				},
				multiple: this.settings.multiple,
			});

			// Set the initial selection if we have an image ID
			const attachmentId = this.elements.imageIdInput.val();
			if (attachmentId) {
				const selection = this.frame.state().get('selection');
				const attachment = wp.media.attachment(attachmentId);
				attachment.fetch();
				selection.add(attachment ? [attachment] : []);
			}

			// When an image is selected
			this.frame.on('select', this.handleImageSelection.bind(this));

			// Open the media uploader
			this.frame.open();
		},

		/**
		 * Handle image selection from the media library
		 */
		handleImageSelection() {
			// Get the selected attachment
			const attachment = this.frame
				.state()
				.get('selection')
				.first()
				.toJSON();

			// Update the hidden input with the attachment ID
			this.elements.imageIdInput.val(attachment.id);

			// Update the image preview
			this.updateImagePreview(attachment);

			// Update button states
			this.updateButtonState();

			// Trigger a change event on the input for any listeners
			this.elements.imageIdInput.trigger('change');
		},

		/**
		 * Update the image preview with the selected image
		 * @param {Object} attachment The attachment object from the media library
		 */
		updateImagePreview(attachment) {
			let imageUrl;

			// Use medium size if available, otherwise use the full size
			if (attachment.sizes && attachment.sizes.medium) {
				imageUrl = attachment.sizes.medium.url;
			} else {
				imageUrl = attachment.url;
			}

			// Update the image source
			this.elements.imagePreview.attr({
				src: imageUrl,
				alt: attachment.alt || attachment.title || 'Signature image',
			});
		},

		/**
		 * Remove the selected image
		 * @param {Event} e Click event
		 */
		removeImage(e) {
			e.preventDefault();

			// Only proceed if we have an image
			if (!this.elements.imageIdInput.val()) {
				return;
			}

			// Clear the hidden input
			this.elements.imageIdInput.val('');

			// Reset the image preview to default
			this.elements.imagePreview.attr({
				src: this.settings.defaultImage,
				srcset: '',
				alt: 'Default placeholder image',
			});

			// Update button states
			this.updateButtonState();

			// Trigger a change event on the input for any listeners
			this.elements.imageIdInput.trigger('change');
		},
	};

	// Initialize when the document is ready
	$(document).ready(function () {
		// Get settings from data attributes if available
		const $uploadButton = $('#uploadimage');
		const dataSettings = {
			title: $uploadButton.data('title') || TNYSINGNATURE.i18n.title,
			buttonText:
				$uploadButton.data('button') || TNYSINGNATURE.i18n.button,
			multiple: $uploadButton.data('multiple') === true,
			defaultImage: TNYSINGNATURE.sigurl + 'assets/img/question.png',
		};

		// Initialize the media uploader with settings
		SignatureMediaUploader.init(dataSettings);
	});
})(jQuery);
