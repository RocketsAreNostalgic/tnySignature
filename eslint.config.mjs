import ranWordPress from '@rocketsarenostalgic/quality-config/eslint/wordpress';

export default [
	{
		ignores: ['assets/dist/**', 'node_modules/**', 'vendor/**'],
	},
	...ranWordPress,
	{
		files: ['assets/src/**/*.js'],
		settings: {
			react: {
				version: '999.999.999',
			},
		},
		rules: {
			'no-console': 'warn',
		},
	},
	{
		files: ['vite.config.js', 'tests/quality/**/*.mjs'],
		rules: {
			'no-console': 'off',
		},
	},
];
