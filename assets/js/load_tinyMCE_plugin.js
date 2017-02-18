(function () {
    tinymce.create('tinymce.plugins.SIGNATURE', {
        init: function (ed, url) {
            // Register commands
            ed.addCommand('SIGNATURE', function () {

                var return_text = '';
                return_text = '[signature]All the best,[/signature]';
                ed.execCommand('mceInsertContent', 0, return_text);
            });
            // Register buttons
            ed.addButton('SIGNATURE', {
                title: 'Signature shortcode',
                image: url + '../../img/icon.png',
                cmd: 'SIGNATURE'
            });
        },
        getInfo: function () {
            return {
                longname: 'SIGNATURE',
                author: 'Ben Rush',
                authorurl: 'http://www.orionrush.com',
                infourl: 'http://www.orionrush.com',
                version: '1.0',
                version: tinymce.majorVersion + "." + tinymce.minorVersion
            };
        }
    });
    // Register plugin
    // first parameter is the button ID and must match ID elsewhere
    // second parameter must match the first parameter of the tinymce.create() function above
    tinymce.PluginManager.add('SIGNATURE', tinymce.plugins.SIGNATURE);
})();