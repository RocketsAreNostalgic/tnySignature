/**
 * AJAX handler to store the state of dismissible notices
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 *
 * http://wordpress.stackexchange.com/a/251191/13551
 * https://pippinsplugins.com/using-ajax-your-plugin-wordpress-admin/
 */
jQuery(document).ready(function ($) {
    jQuery(document).on('click', '.signature-notice .notice-dismiss', function () {
        // Read the "data-notice" to track which notice is being dismissed
        var notice_type = $(this).closest('.signature-notice').data('notice');

        data = {
            action: 'sig_dismissed_notice_handler',
            notice_type: notice_type,
            tnysig_nonce: tnysig_vars.tnysig_nonce
        };

        jQuery.post(ajaxurl, data, function (response) {
            // alert(response);
        });
        return false;
    });
});