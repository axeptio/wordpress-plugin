import { cpSync } from 'node:fs';
import { defineConfig } from 'vite';

// WordPress enqueues these bundles from fixed paths (see script_url() and
// style_url() in includes/core.php), so they keep their names and stay unhashed.

const staticAssets = ['assets/img', 'assets/fonts'];

const copyStaticAssets = {
	name: 'axeptio-copy-static-assets',
	writeBundle() {
		staticAssets.forEach(dir => cpSync(dir, dir.replace('assets/', 'dist/'), { recursive: true }));
	}
};

export default defineConfig({
	base: './',
	publicDir: false,
	plugins: [copyStaticAssets],
	build: {
		outDir: 'dist',
		assetsDir: '.',
		rollupOptions: {
			input: {
				'js/backend/app': 'assets/js/backend/app.js',
				'js/frontend/axeptio': 'assets/js/frontend/axeptio.js',
				'css/backend/main': 'assets/css/backend/main.css',
				'css/frontend/main': 'assets/css/frontend/main.css'
			},
			output: {
				entryFileNames: '[name].js',
				assetFileNames: '[name][extname]',
				// Classic scripts share the global scope, so the bundle is
				// wrapped: a minified `_` would clobber Underscore.
				banner: '(function(){',
				footer: '})();'
			}
		}
	}
});
