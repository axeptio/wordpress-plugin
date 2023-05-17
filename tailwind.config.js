/** @type {import('tailwindcss').Config} */
const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
	important: '#axeptio-app',
	content: [
		"./includes/**/*.php",
		"./assets/src/**/*.js",
		"./templates/**/*.{php,js}"
	],
	corePlugins: {
		preflight: false,
	},
	theme: {
		extend: {
			fontFamily: {
				'serif': ['"Source Serif 4"', ...defaultTheme.fontFamily.serif],
			},
		},
	},
	plugins: [
		require('@tailwindcss/aspect-ratio'),
	],
}
