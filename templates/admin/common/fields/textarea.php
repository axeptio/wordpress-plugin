<?php defined( 'ABSPATH' ) || exit; ?>
<div class="col-span-full">
	<label for="<?php echo esc_html( $data->id ); ?>" class="flex items-center gap-1 text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ); ?>
		<?php if ( isset( $data->help_url ) ) : ?>
			<a href="<?php echo esc_url( $data->help_url ); ?>" target="_blank" rel="noopener noreferrer">
				<img src="<?php echo esc_url( XPWP_URL . 'dist/img/icons/info.svg' ); ?>" class="size-4" alt="<?php esc_attr_e( 'More information', 'axeptio-sdk-integration' ); ?>">
			</a>
		<?php endif ?>
	</label>
	<div class="mt-2">
		<textarea id="<?php echo esc_attr( $data->id ); ?>" name="<?php echo isset( $data->group ) ? esc_attr( $data->group . '[' . $data->name . ']' ) : esc_attr( $data->name ); ?>" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-600 sm:text-sm sm:leading-6"<?php echo isset( $data->placeholder ) ? ' placeholder="' . esc_attr( $data->placeholder ) . '"' : ''; ?>><?php echo esc_html( $data->value ); ?></textarea>
	</div>
</div>
