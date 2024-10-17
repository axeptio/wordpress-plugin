<div x-data="{ gtmEvents: '<?php echo esc_js( $data->value ); ?>' }">
	<label for="<?php echo esc_attr( $data->id ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ); ?>
	</label>
	<p class="mt-1 text-sm text-gray-500"><?php echo esc_html( $data->description ); ?></p>
	<select
		id="<?php echo esc_attr( $data->id ); ?>"
		name="<?php echo esc_attr( $data->group . '[' . $data->name . ']' ); ?>"
		x-model="gtmEvents"
		class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-amber-600 sm:text-sm sm:leading-6"
	>
		<option value="true"><?php esc_html_e( 'Send all events to dataLayer', 'axeptio-wordpress-plugin' ); ?></option>
		<option value="false"><?php esc_html_e( 'Do not send any events to dataLayer', 'axeptio-wordpress-plugin' ); ?></option>
		<option value="update_only"><?php esc_html_e( 'Send only axeptio_update event to dataLayer', 'axeptio-wordpress-plugin' ); ?></option>
	</select>
</div>