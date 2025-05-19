import { globSync } from 'glob';
import { resolve } from 'path';
import { defineConfig } from 'vite';

// Function to get entry points from glob patterns
function getEntries() {
  const entries = {};

  // Get all JS files in admin/js and public/js
  const jsFiles = globSync('assets/src/{admin,public}/js/*.js');
  console.log('JS files found:', jsFiles);
  jsFiles.forEach(file => {
    // Create a unique key for JS files by including the directory path
    const pathParts = file.split('/');
    const dir = pathParts[pathParts.length - 2]; // 'js'
    const parentDir = pathParts[pathParts.length - 3]; // 'admin' or 'public'
    const name = pathParts[pathParts.length - 1].replace('.js', '');
    const key = `${parentDir}_${dir}_${name}`;
    entries[key] = resolve(__dirname, file);
  });

  // Get all SCSS files in admin/styles and public/styles
  const styleFiles = globSync('assets/src/{admin,public}/styles/*.scss');
  console.log('SCSS files found:', styleFiles);
  styleFiles.forEach(file => {
    // Create a unique key for SCSS files by including the directory path
    const pathParts = file.split('/');
    const dir = pathParts[pathParts.length - 2]; // 'styles'
    const parentDir = pathParts[pathParts.length - 3]; // 'admin' or 'public'
    const name = pathParts[pathParts.length - 1].replace('.scss', '');
    const key = `${parentDir}_${dir}_${name}`;
    entries[key] = resolve(__dirname, file);
  });

  console.log('Final entries:', entries);
  return entries;
}

export default defineConfig({
  build: {
    outDir: resolve(__dirname, 'assets/dist'),
    emptyOutDir: true,
    sourcemap: false,
    minify: true,
    rollupOptions: {
      input: getEntries(),
      output: {
        // Maintain directory structure for output files
        entryFileNames: (chunkInfo) => {
          // Get the name from the chunk
          const name = chunkInfo.name || '';

          // Parse the name to extract directory information
          // Format is: parentDir_dir_name (e.g., admin_js_admin)
          const parts = name.split('_');
          if (parts.length >= 3) {
            const parentDir = parts[0]; // admin or public
            const dir = parts[1];       // js
            const baseName = parts.slice(2).join('_'); // The actual filename

            return `${parentDir}/${dir}/${baseName}.min.js`;
          }

          // Fallback: use the input file path to determine the output path
          const inputFile = chunkInfo.facadeModuleId || '';
          if (inputFile.includes('/admin/js/')) {
            return 'admin/js/[name].min.js';
          } else if (inputFile.includes('/public/js/')) {
            return 'public/js/[name].min.js';
          }

          return '[name].min.js';
        },
        chunkFileNames: 'js/[name]-[hash].js',
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && /\.(css|scss)$/.test(assetInfo.name)) {
            // Get the name without extension
            const name = assetInfo.name.split('/').pop().replace(/\.(css|scss)$/, '');

            // Parse the name to extract directory information
            // Format is: parentDir_dir_name (e.g., admin_styles_admin)
            const parts = name.split('_');
            if (parts.length >= 3) {
              const parentDir = parts[0]; // admin or public
              const dir = parts[1];       // styles
              const baseName = parts.slice(2).join('_'); // The actual filename

              return `${parentDir}/${dir}/${baseName}.min.css`;
            }

            // Fallback for any other CSS files
            return '[name].min.css';
          }

          // For other assets like images
          return 'assets/[name].[ext]';
        },
      },
    },
  },
});
