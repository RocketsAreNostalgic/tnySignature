/**
 * Media uploader for the signature image
 *
 * @since 0.0.2
 * @author bnjmnrsh
 *
 * http://stevenslack.com/add-image-uploader-to-profile-admin-page-wordpress/
 * https://gist.github.com/mikejolley/3a3b366cb62661727263#file-gistfile1-php
 */

/* global jQuery, SINGNATURE */

jQuery(document).ready(function () {
	// Uploading files.
	let fileFrame;

	const imgId = jQuery('#user_signature_image_id').val();
	if (imgId) {
		jQuery('#removeimage').prop('disabled', false);
	} else {
		jQuery('#removeimage').prop('disabled', true);
	}

	jQuery('.signature-image').on('click', function (e) {
		e.preventDefault();

		// If the media frame already exists, reopen it.
		if (fileFrame) {
			fileFrame.open();
			return;
		}
		// Create the media frame.
		fileFrame = wp.media.frames.fileFrame = wp.media({
			title: jQuery(this).data('uploader_title'),
			button: {
				text: jQuery(this).data('uploader_button_text'),
			},
			multiple: false, // Set to true to allow multiple files to be selected.
		});
		// When an image is selected, run a callback.
		fileFrame.on('select', function () {
			// We set multiple to false so only get one image from the uploader.
			const attachment = fileFrame
				.state()
				.get('selection')
				.first()
				.toJSON();

			// Do something with attachment.id and/or attachment.url here.
			// Add the id of the image to the hidden field.
			jQuery('#user_signature_image_id').attr('value', attachment.id);
			// Update the image preview with the new image.
			if (attachment.sizes.hasOwnProperty('medium')) {
				// If we have a medium, then the image was large enough that wp created one.
				jQuery('#signature-image-preview').attr(
					'src',
					attachment.sizes.medium.url
				);
			} else {
				// Otherwise, we can use the original image.
				jQuery('#signature-image-preview').attr('src', attachment.url);
			}
			// Disable the buttons.
			jQuery('#removeimage').prop('disabled', true);
			jQuery('#uploadimage').prop('disabled', true);
			// Submit the form
			jQuery('#submit').trigger('click');
		});
		// Finally, open the modal.
		fileFrame.open();
	});

	jQuery('#removeimage').on('click', function () {
		// Check to see if there is a value in the hidden image id field.
		const currentImgId = jQuery('#user_signature_image_id').val();
		if (currentImgId) {
			// Strip the value.
			jQuery('#user_signature_image_id').val('');
			// Set the image preview to default.
			jQuery('#signature-image-preview')
				.attr('src', SINGNATURE.sigurl + 'assets/img/question.png')
				.attr('srcset', '');
			// Disable the buttons.
			jQuery('#removeimage').prop('disabled', true);
			jQuery('#uploadimage').prop('disabled', true);
			// Submit the form.
			jQuery('#submit').trigger('click');
		}
	});
});
