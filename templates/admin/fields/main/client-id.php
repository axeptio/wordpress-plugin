<input type="hidden" name="axeptio_settings[client_id]" x-model="accountID" value="<?php echo esc_attr( get_option( 'xpwp_client_id' ) ); ?>">
<div class="container relative">
	<div
		class="-translate-x-2 ring-0 transition ease-out duration-300 w-full flex items-center justify-between sm:mt-0 sm:flex-auto max-w-sm"
		:class="{ 'translate-x-0': !showID, '-translate-x-2': showID }"
	>
		<input
			name="axeptio_settings[client_id]"
			id="xpwp_client_id"
			x-model="accountID"
			type="text"
			placeholder="Entrez votre ID de compte"
			x-bind:disabled="showID"
			class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:!ring-amber-400 sm:text-sm sm:leading-6"
			:class="{ 'ring-1 shadow-sm': !showID, 'ring-0 shadow-none': showID }"
			value="<?php echo esc_attr( \Axeptio\get_option( 'client_id', '' ) ); ?>"
		>
		<button
			type="button"
			@click="showID ? editAccountID : validateAccountID"
			class="rounded-md bg-white px-3 py-2 text-sm font-semibold ml-2 inline-flex items-center transition ease-out duration-300"
			:class="{ 'hover:bg-gray-50 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300': !showID, 'ring-0 shadow-none text-teal-600': showID }"
		>
			<svg :class="{ 'hidden': showID, 'block': !showID }" class="-ml-1 mr-0.5 fill-gray-900 h-4 hidden" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M9,16.17L4.83,12l-1.42,1.41L9,19L21,7l-1.41-1.41L9,16.17z"></path>
			</svg>
			<svg :class="{ 'hidden': !showID, 'block': showID }" class="-ml-1 mr-1 h-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
			</svg>
			<span
				x-text="showID ? '<?php esc_html_e( 'Edit', 'axeptio-wordpress-plugin' ); ?>' : '<?php esc_html_e( 'Validate', 'axeptio-wordpress-plugin' ); ?>'"
			></span>
		</button>
	</div>
	<p
		x-show="errorMessage"
		x-text="errorMessage"
		x-transition:enter="transition ease-out duration-200"
		x-transition:enter-start="opacity-0"
		x-transition:enter-end="opacity-1"
		x-transition:leave="transition ease-in duration-300"
		x-transition:leave-start="opacity-1"
		x-transition:leave-end="opacity-0" class="text-red-500 mt-2"
	></p>
</div>
