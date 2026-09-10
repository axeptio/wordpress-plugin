import { cpSync } from 'node:fs';
import { defineConfig } from 'vite';

// WordPress enqueues these bundles from fixed paths (see script_url() and
// style_url() in includes/core.php), so they keep their names and stay unhashed.

const staticAssets = ['assets/img', 'assets/fonts'];

const copyStaticAssets = {
	name: 'axeptio-copy-static-assets',
	// Runs on every write, so a watch build refreshes these too. Rollup only
	// watches the module graph, which these are not part of, so editing one
	// is picked up on the next rebuild rather than on save.
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
				// WordPress loads these as classic scripts, where top-level
				// declarations would land on window and clobber globals such
				// as Underscore's `_`. Rollup only wraps entry points in an
				// IIFE for single-entry builds, so the scope is closed here.
				banner: '(function(){',
				footer: '})();'
			}
		}
	}
});
