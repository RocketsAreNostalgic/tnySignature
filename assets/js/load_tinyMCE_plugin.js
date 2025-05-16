/**
 * Adds the signature button to tinyMCE editor
 *
 * @since 0.0.2
 * @author bnjmnrsh
 * @package TNY_SIGNATURE
 */
(function () {
    tinymce.create('tinymce.plugins.SIGNATURE', {
        /**
         * Initialize the plugin.
         *
         * @param object ed The tinymce editor
         * @param string url The absolute url of our plugin directory
         */
        init: function (ed, url) {
            // Register commands.
            ed.addCommand('SIGNATURE', function () {

                var return_text = '';
                return_text = '[signature]All the best,[/signature]';
                ed.execCommand('mceInsertContent', 0, return_text);
            });
            // Register buttons.
            ed.addButton('SIGNATURE', {
                title: 'Signature shortcode',
                image: url + '../../img/icon.png',
                cmd: 'SIGNATURE'
            });
        },
        /**
         * Returns information about the plugin as a name/value array.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo: function () {
            return {
                longname: 'SIGNATURE',
                author: 'Benjamin Rush',
                authorurl: 'http://github.com/bnjmnrsh',
                infourl: 'https://github.com/RocketsAreNostalgic/tnySignature',
                version: '2.0',
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            };
        }
    });
    /**
     * Register plugin:
     *  - first parameter is the button ID and must match ID elsewhere
     *  - second parameter must match the first parameter of the tinymce.create() function above
     */
    tinymce.PluginManager.add('SIGNATURE', tinymce.plugins.SIGNATURE);
})();