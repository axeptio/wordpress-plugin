<div x-data="imageUploadComponent({
	initialValue: '<?php echo esc_js( $data->value ); ?>',
	fieldName: '<?php echo esc_js( $data->group . '[' . $data->name . ']' ); ?>',
	fieldId: '<?php echo esc_js( $data->id ); ?>'
})">
	<label :for="fieldId" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ); ?>
	</label>
	<div class="flex items-center my-2">
		<input type="checkbox" id="disable_<?php echo esc_attr( $data->id ); ?>"
				x-model="disableImage"
				class="mr-2">
		<label for="disable_<?php echo esc_attr( $data->id ); ?>" class="text-sm font-medium text-gray-700">
			<?php echo esc_html__( 'Disable this image', 'axeptio-wordpress-plugin' ); ?>
		</label>
	</div>

	<div x-show="!disableImage">
		<div class="mt-2 flex items-center">
			<input type="hidden" :name="fieldName" :id="fieldId" :value="getValue()">
			<button type="button"
					@click="openMediaUploader"
					class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
				<?php echo esc_html__( 'Choose Image', 'axeptio-wordpress-plugin' ); ?>
			</button>
			<div class="preview-image ml-3" x-show="imageUrl">
				<img :src="imageUrl" alt="" style="max-width: 100px; max-height: 100px;">
			</div>
			<button type="button"
					@click="removeImage"
					x-show="imageUrl"
					class="ml-3 text-sm text-red-600">
				<?php echo esc_html__( 'Remove', 'axeptio-wordpress-plugin' ); ?>
			</button>
		</div>
	</div>
</div>
