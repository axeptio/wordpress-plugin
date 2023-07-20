<div class="container relative">
	<div
		class="ring-0 transition ease-out duration-300 w-full flex items-center justify-between sm:mt-0 sm:flex-auto max-w-sm"
	>
		<input
			name="axeptio_settings[cookie_domain]"
			id="xpwp_cookie_domain"
			type="text"
			placeholder="Entrez votre ID de compte"
			class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:!ring-amber-400 sm:text-sm sm:leading-6 focus:ring-1 focus:shadow-sm"
			value="<?php echo esc_attr( \Axeptio\get_cookie_domain() ); ?>"
		>
	</div>
</div>
