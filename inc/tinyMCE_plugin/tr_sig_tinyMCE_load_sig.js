(
	function () {
		tinymce.create( 'tinymce.plugins.TRSIG', {
			init: function ( ed, url ) {
				// Register commands
				ed.addCommand( 'TRSIG', function () {

					var return_text = '';
					return_text = '[signature]All the best,[/signature]';
					ed.execCommand( 'mceInsertContent', 0, return_text );
				} );
				// Register buttons
				ed.addButton( 'TRSIG', {
					title: 'Signature shortcode',
					image: url + '/img/icon.png',
					cmd: 'TRSIG'
				} );
			},
			getInfo: function () {
				return {
					longname:   'TRSIG',
					author:     'Ben Rush',
					authorurl:  'http://www.orionrush.com',
					infourl:    'http://www.orionrush.com',
					version:    '1.0',
					version: tinymce.majorVersion + "." + tinymce.minorVersion
				};
			}
		} );
		// Register plugin
		// first parameter is the button ID and must match ID elsewhere
		// second parameter must match the first parameter of the tinymce.create() function above
		tinymce.PluginManager.add( 'TRSIG', tinymce.plugins.TRSIG );
	}
)();