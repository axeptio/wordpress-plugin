<div x-data="imageUploadComponent({
	initialValue: '<?php echo esc_js( $data->value ); ?>',
	fieldName: '<?php echo esc_js( $data->group . '[' . $data->name . ']' ); ?>',
	fieldId: '<?php echo esc_js( $data->id ); ?>'
})">
	<label :for="fieldId" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ); ?>
		<?php if ( isset( $data->help_url ) ) : ?>
			<a href="<?php echo esc_url( $data->help_url ); ?>" target="_blank">
				<span class="dashicons dashicons-info-outline"></span>
			</a>
		<?php endif ?>
	</label>
	<div class="inline-flex items-center w-full relative my-2">
		<?php
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/toggle',
			array(
				'label'        => __( 'Disable this image', 'axeptio-sdk-integration' ),
				'name'         => '',
				'description'  => '',
				'id'           => 'disable_' . $data->id,
				'alpine_state' => 'disableImage',
				'checked'      => 'disabled' === $data->value,
			)
		);
		?>
	</div>

	<div x-show="!disableImage">
		<div class="mt-2 flex items-center">
			<input type="hidden" :name="fieldName" :id="fieldId" :value="getValue()">
			<button type="button"
					@click="openMediaUploader"
					class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
				<?php echo esc_html__( 'Choose Image', 'axeptio-sdk-integration' ); ?>
			</button>
			<div class="preview-image ml-3" x-show="imageUrl">
				<img :src="imageUrl" alt="" style="max-width: 100px; max-height: 100px;">
			</div>
			<button type="button"
					@click="removeImage"
					x-show="imageUrl"
					class="ml-3 text-sm text-red-600">
				<?php echo esc_html__( 'Remove', 'axeptio-sdk-integration' ); ?>
			</button>
		</div>
	</div>
</div>
