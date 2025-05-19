/**
 * Adds the signature button to tinyMCE editor
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package
 */

/* global tinymce, tnySignatureL10n */
(function () {
	tinymce.create('tinymce.plugins.SIGNATURE', {
		/**
		 * Initialize the plugin.
		 *
		 * @param {Object} ed The tinymce editor
		 */

		init(ed) {
			// Register commands.
			ed.addCommand('SIGNATURE', function () {
				// Use the user's farewell message if available, otherwise fall back to default
				const farewell =
					tnySignatureL10n.userFarewell ||
					tnySignatureL10n.defaultFarewell ||
					'All the best,';
				const returnText = '[signature]' + farewell + '[/signature]';
				ed.execCommand('mceInsertContent', 0, returnText);
			});
			// Register buttons.
			ed.addButton('SIGNATURE', {
				title: tnySignatureL10n.buttonTitle || 'Signature shortcode',
				image: tnySignatureL10n.pluginUrl + 'assets/img/icon.png',
				cmd: 'SIGNATURE',
			});
		},
		/**
		 * Returns information about the plugin as a name/value array.
		 *
		 * @return {Object} Name/value array containing information about the plugin.
		 */
		getInfo() {
			return {
				longname: 'SIGNATURE',
				author: 'Benjamin Rush',
				authorurl: 'http://github.com/bnjmnrsh',
				infourl: 'https://github.com/RocketsAreNostalgic/tnySignature',
				version: tinymce.majorVersion + '.' + tinymce.minorVersion,
			};
		},
	});
	/**
	 * Register plugin:
	 *  - first parameter is the button ID and must match ID elsewhere
	 *  - second parameter must match the first parameter of the tinymce.create() function above
	 */
	tinymce.PluginManager.add('SIGNATURE', tinymce.plugins.SIGNATURE);
})();
