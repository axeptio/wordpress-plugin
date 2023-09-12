<div class="flex items-center w-full relative">
	<label
		:for="'enable_' + plugin.Metas.plugin" type="button" class="relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2 bg-gray-400"
		role="switch" aria-checked="true"
		:aria-checked="plugin.Metas.enabled.toString()"
		x-state:on="Enabled" x-state:off="Not Enabled"
		:class="{ 'opacity-50' : globalEnabled(plugin), 'bg-amber-400': localOrGlobalEnabled(plugin), 'bg-gray-400': !localOrGlobalEnabled(plugin) }"
	>
		<span
			aria-hidden="true"
			x-state:on="Enabled"
			x-state:off="Not Enabled"
			class="pointer-events-none flex items-center justify-center shadow-md relative -left-1 -top-1 inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"
			:class="{ 'translate-x-6': localOrGlobalEnabled(plugin), 'translate-x-0': !localOrGlobalEnabled(plugin) }"
		>
			<svg class="hidden fill-gray-400" :class="{ 'hidden': localOrGlobalEnabled(plugin), 'block': !localOrGlobalEnabled(plugin) }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<g>
					<path d="M19,6.41L17.59,5L12,10.59L6.41,5L5,6.41L10.59,12L5,17.59L6.41,19L12,13.41L17.59,19L19,17.59L13.41,12L19,6.41z"></path>
				</g>
			</svg>
			<svg class="hidden fill-amber-400" :class="{ 'block': localOrGlobalEnabled(plugin), 'hidden': !localOrGlobalEnabled(plugin) }" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M9,16.17L4.83,12l-1.42,1.41L9,19L21,7l-1.41-1.41L9,16.17z"></path>
			</svg>
		</span>
	</label>
	<div class="ml-5 sr-only">
		<label :for="plugin.Metas.plugin"><?php echo esc_html__( 'Enable', 'axeptio-wordpress-plugin' ); ?></label>
	</div>
	<input type="checkbox" @change="enableControl(plugin)" class="appearance-none w-full h-full active:outline-none focus:outline-none opacity-0 absolute -left-full top-0" :id="'enable_' + plugin.Metas.plugin" :name="'axeptio_plugins[\'' + index + '\']'" value="1" placeholder="">
</div>
