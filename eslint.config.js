const { FlatCompat } = require('@eslint/eslintrc');
const path = require('path');

const compat = new FlatCompat({
	baseDirectory: __dirname,
	recommendedConfig: {}
});

module.exports = [
	{
		ignores: [
			'assets/js/vendor/**',
			'assets/js/admin/vendor/**',
			'assets/js/frontend/vendor/**',
			'assets/js/shared/vendor/**',
			'webpack.config.babel.js',
			'tests/**',
			'node_modules/**',
			'dist/**'
		]
	},
	...compat.extends('plugin:@wordpress/eslint-plugin/recommended'),
	{
		languageOptions: {
			globals: {
				module: 'readonly',
				process: 'readonly'
			}
		}
	}
];
