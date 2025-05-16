import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
	build: {
		// Set outDir to a directory inside assets to avoid the warning
		outDir: resolve(__dirname, 'assets/build'),
		emptyOutDir: true,
		sourcemap: false,
		minify: true,
		rollupOptions: {
			input: {
				// JavaScript files
				load_tinyMCE_plugin: resolve(__dirname, 'assets/js/load_tinyMCE_plugin.js'),
				media_uploader: resolve(__dirname, 'assets/js/media_uploader.js'),
				media_uploader_improved: resolve(__dirname, 'assets/js/media_uploader_improved.js'),
				'notice-dismiss': resolve(__dirname, 'assets/js/notice-dismiss.js'),

				// CSS files
				signature_admin: resolve(__dirname, 'assets/css/signature_admin.scss'),
				signature_rendered: resolve(__dirname, 'assets/css/signature_rendered.scss'),
			},
			output: {
				// Place minified JS files in the build directory
				entryFileNames: (chunkInfo) => {
					// Extract directory from the input file path
					const inputFile = chunkInfo.facadeModuleId;
					if (inputFile && inputFile.includes('/js/')) {
						return 'js/[name].min.js';
					}
					return '[name].min.js';
				},
				chunkFileNames: 'js/[name].min.js',
				assetFileNames: (assetInfo) => {
					// For CSS files, place them in the css directory
					if (/\.(css|scss)$/.test(assetInfo.name)) {
						return 'css/[name].min.css';
					}
					// For other assets, maintain their directory structure
					return '[ext]/[name].min.[ext]';
				},
			},
		},
	},
});
