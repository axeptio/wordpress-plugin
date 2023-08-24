<div class="col-span-full">
	<label for="<?php echo esc_html( $data->id ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ); ?>
	</label>
	<div class="mt-2">
		<textarea id="<?php echo esc_attr( $data->id ); ?>" name="<?php echo isset( $data->group ) ? esc_attr( $data->group . '[' . $data->name . ']' ) : esc_attr( $data->name ); ?>" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"><?php echo esc_html( $data->value ); ?></textarea>
	</div>
</div>
