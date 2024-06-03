<div class="inline-flex items-center w-full relative">
	<label for="xpwp_disable_send_datas" class="relative inline-flex h-7 w-14 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 bg-gray-400"  role="switch" aria-checked="true" :aria-checked="sendDatas.toString()" x-state:on="Enabled" x-state:off="Not Enabled" :class="{ 'bg-amber-400': sendDatas, 'bg-gray-400': !(sendDatas) }">
		<span
			aria-hidden="true"
			x-state:on="Enabled"
			x-state:off="Not Enabled"
			class="pointer-events-none flex items-center justify-center shadow-md relative -left-1 -top-1 inline-block h-8 w-8 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"
			:class="{ 'translate-x-8': sendDatas, 'translate-x-0': !(sendDatas) }"
		>
			<svg class="hidden fill-gray-400" :class="{ 'hidden': sendDatas, 'block': !(sendDatas) }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<g>
					<path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41z"></path>
				</g>
			</svg>
			<svg class="hidden fill-amber-400" :class="{ 'block': sendDatas, 'hidden': !(sendDatas) }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M9,16.17L4.83,12l-1.42,1.41L9,19L21,7l-1.41-1.41L9,16.17z"></path>
			</svg>
		</span>
	</label>
	<div class="ml-5">
		<label for="xpwp_disable_send_datas"><?php echo esc_html__( 'By checking this box, I do not wish to send data/errors to the Axeptio plugin and do not want to contribute to the improvement of the plugin.', 'axeptio-wordpress-plugin' ); ?></label>
	</div>
	<input type="checkbox" @change="sendDatas = !sendDatas" class="appearance-none w-full h-full active:outline-none focus:outline-none opacity-0 absolute -left-full top-0" id="xpwp_disable_send_datas" name="axeptio_settings[disable_send_datas]" value="1" <?php echo (bool) \Axeptio\Plugin\get_option( 'disable_send_datas', '0' ) ? 'checked' : ''; ?> placeholder="">
</div>
