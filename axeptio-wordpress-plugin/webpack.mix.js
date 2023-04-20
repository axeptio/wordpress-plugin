let mix = require('laravel-mix');
let ImageminPlugin = require('imagemin-webpack-plugin').default;

mix.browserSync({
	proxy: 'https://axeptio.docker.localhost',
	https: true,
	files: [
		'./templates/**/*.php',
		'./includes/**/*.php',
		'./dist/js/**/*.js',
		'./dist/css/**/*.css',
	],
});

mix.webpackConfig({
	plugins: [
		new ImageminPlugin({
			// disable: process.env.NODE_ENV !== 'production', // Disable during development
			pngquant: {
				quality: '95-100',
			},
			test: /\.(jpe?g|png|gif|svg)$/i,
		}),
	]
})

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */
mix.setPublicPath('dist');

mix
	.copy('assets/img', 'dist/img')
	.copy('assets/fonts', 'dist/fonts')
	.js('assets/js/main.js', 'dist/js/main.js')
	.postCss("assets/css/main.css", "dist/css/", [
		require('postcss-nested'),
		require('autoprefixer'),
		require('tailwindcss'),
	])
	.options({
		processCssUrls: false
	});
