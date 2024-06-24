<div>
	<label for="<?php echo esc_attr( $data->id ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ); ?>
	</label>
	<div class="inline-flex items-center w-full relative">
		<label for="<?php echo esc_attr( $data->id ); ?>" class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 bg-gray-400"  role="switch" aria-checked="true" :aria-checked="proxySdk.toString()" x-state:on="Enabled" x-state:off="Not Enabled" :class="{ 'bg-amber-400': proxySdk, 'bg-gray-400': !(proxySdk) }">
		<span
			aria-hidden="true"
			x-state:on="Enabled"
			x-state:off="Not Enabled"
			class="pointer-events-none flex items-center justify-center shadow-md relative -left-1 -top-1 inline-block h-8 w-8 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"
			:class="{ 'translate-x-8': proxySdk, 'translate-x-0': !(proxySdk) }"
		>
			<svg class="hidden fill-gray-400" :class="{ 'hidden': proxySdk, 'block': !(proxySdk) }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<g>
					<path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41z"></path>
				</g>
			</svg>
			<svg class="hidden fill-amber-400" :class="{ 'block': proxySdk, 'hidden': !(proxySdk) }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M9,16.17L4.83,12l-1.42,1.41L9,19L21,7l-1.41-1.41L9,16.17z"></path>
			</svg>
		</span>
		</label>
		<div class="ml-5">
			<label for="<?php echo esc_attr( $data->id ); ?>">
				<?php echo esc_attr( $data->instruction ); ?>
			</label>
		</div>
		<input type="checkbox" @change="proxySdk = !proxySdk" class="appearance-none w-full h-full active:outline-none focus:outline-none opacity-0 absolute -left-full top-0" id="<?php echo esc_attr( $data->id ); ?>" name="axeptio_settings[proxy_sdk]" value="1" <?php echo (bool) \Axeptio\Plugin\get_option( 'proxy_sdk', '0' ) ? 'checked' : ''; ?> placeholder="">
	</div>
</div>
