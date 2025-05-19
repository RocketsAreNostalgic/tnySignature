/**
 * AJAX handler to store the state of dismissible notices
 *
 * @since 0.0.2
 * @author bnjmnrsh
 *
 * http://wordpress.stackexchange.com/a/251191/13551
 * https://pippinsplugins.com/using-ajax-your-plugin-wordpress-admin/
 */

/* global jQuery, ajaxurl tnysig_vars */
/* eslint camelcase: 0 */ // Disable camelcase rule for this file due to WordPress naming conventions
jQuery(document).ready(function () {
	jQuery(document).on(
		'click',
		'.signature-notice .notice-dismiss',
		function () {
			// Read the "data-notice" to track which notice is being dismissed
			const noticeType = jQuery(this)
				.closest('.signature-notice')
				.data('notice');

			const data = {
				action: 'sig_dismissed_notice_handler',
				notice_type: noticeType,
				// eslint-disable-next-line camelcase
				tnysig_nonce: tnysig_vars.tnysig_nonce,
			};

			jQuery.post(ajaxurl, data, function () {
				// alert(response);
			});
			return false;
		}
	);
});
