<div class="sm:col-span-4">
	<label for="<?php echo esc_attr( $data->id ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ); ?>
	</label>
	<div class="mt-2">
		<input id="<?php echo esc_attr( $data->id ); ?>" name="<?php echo isset( $data->group ) ? esc_attr( $data->group . '[' . $data->name . ']' ) : esc_attr( $data->name ); ?>" type="<?php echo esc_attr( $data->type ?? 'text' ); ?>" value="<?php echo esc_attr( $data->value ); ?>" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
	</div>
	<?php if (!empty($data->instruction)): ?>
	<div class="mt-2 text-gray-500 text-xs">
		<?php echo esc_html( $data->instruction ); ?>
	</div>
	<?php endif; ?>
</div>
