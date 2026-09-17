'use strict';

module.exports = {
	extends: [
		require.resolve(
			'@rocketsarenostalgic/quality-config/stylelint/wordpress-scss'
		),
	],
	ignoreFiles: ['assets/dist/**', 'node_modules/**', 'vendor/**'],
	rules: {
		'selector-class-pattern': null,
	},
};
