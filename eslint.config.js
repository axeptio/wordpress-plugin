import js from '@eslint/js';

export default [
	js.configs.recommended,
	{
		ignores: [
			'assets/js/vendor/**',
			'assets/js/admin/vendor/**',
			'assets/js/frontend/vendor/**',
			'assets/js/shared/vendor/**',
			'webpack.config.babel.js',
			'tests/**'
		]
	}
];
