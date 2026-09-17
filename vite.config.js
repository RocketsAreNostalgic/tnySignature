import path from 'node:path';
import { fileURLToPath } from 'node:url';

import { globSync } from 'glob';
import { defineConfig } from 'vite';

const projectRoot = path.dirname(fileURLToPath(import.meta.url));

function getEntries() {
	const entries = {};

	for (const file of globSync('assets/src/{admin,public}/js/*.js')) {
		const parts = file.split('/');
		const parentDirectory = parts.at(-3);
		const directory = parts.at(-2);
		const name = path.basename(file, '.js');
		entries[`${parentDirectory}_${directory}_${name}`] = path.resolve(
			projectRoot,
			file
		);
	}

	for (const file of globSync('assets/src/{admin,public}/styles/*.scss')) {
		const parts = file.split('/');
		const parentDirectory = parts.at(-3);
		const directory = parts.at(-2);
		const name = path.basename(file, '.scss');
		entries[`${parentDirectory}_${directory}_${name}`] = path.resolve(
			projectRoot,
			file
		);
	}

	return entries;
}

export default defineConfig({
	build: {
		outDir: path.resolve(projectRoot, 'assets/dist'),
		emptyOutDir: true,
		sourcemap: false,
		minify: true,
		rollupOptions: {
			input: getEntries(),
			output: {
				entryFileNames: (chunkInfo) => {
					const parts = (chunkInfo.name || '').split('_');
					if (parts.length >= 3) {
						const parentDirectory = parts[0];
						const directory = parts[1];
						const baseName = parts.slice(2).join('_');
						return `${parentDirectory}/${directory}/${baseName}.min.js`;
					}

					const inputFile = chunkInfo.facadeModuleId || '';
					if (inputFile.includes('/admin/js/')) {
						return 'admin/js/[name].min.js';
					}
					if (inputFile.includes('/public/js/')) {
						return 'public/js/[name].min.js';
					}
					return '[name].min.js';
				},
				chunkFileNames: 'js/[name]-[hash].js',
				assetFileNames: (assetInfo) => {
					if (assetInfo.name && /\.(css|scss)$/.test(assetInfo.name)) {
						const name = path
							.basename(assetInfo.name)
							.replace(/\.(css|scss)$/, '');
						const parts = name.split('_');
						if (parts.length >= 3) {
							const parentDirectory = parts[0];
							const directory = parts[1];
							const baseName = parts.slice(2).join('_');
							return `${parentDirectory}/${directory}/${baseName}.min.css`;
						}
						return '[name].min.css';
					}

					return 'assets/[name].[ext]';
				},
			},
		},
	},
});
