<div class="max-w-sm">
	<input type="hidden" x-model="selectedOption" id="xpwp_version" value="<?php echo esc_attr( $data->version ); ?>">
	<select
		name="xpwp_version"
		class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:max-w-xs sm:text-sm sm:leading-6"
	>
		<option value=""><?php esc_html_e( 'Select a version', 'axeptio-wordpress-plugin' ); ?></option>
		<template x-for="option in options" :key="option.value">
			<option
				:value="option.value"
				x-text="option.text"
				:selected="option.value === selectedOption"
			></option>
		</template>
	</select>
</div>
