<?php
$axeptio_has_multilingual = \Axeptio\Models\i18n::has_multilangual();
?>
<div class="grid grid-cols-3 gap-4" x-data="{ selectedLang: '0' }">
	<?php if ( $axeptio_has_multilingual ) : ?>
		<div
			x-data="selectLang({ data: <?php echo esc_attr( wp_json_encode( array_values( \Axeptio\Models\i18n::get_languages() ) ) ); ?>, emptyOptionsMessage: 'No countries match your search.', name: 'lang', placeholder: 'Select a language', value: 0 })"
			x-init="init()"
			@click.away="closeListbox()"
			@keydown.escape="closeListbox()"
			class="relative"
		>
				<span class="inline-block w-full rounded-md shadow-sm">
						<div
							x-ref="button"
							@click="toggleListboxVisibility()"
							:aria-expanded="open"
							aria-haspopup="listbox"
							class="relative z-0 w-full py-2 pl-3 pr-10 text-left transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md cursor-default focus:outline-none focus:shadow-outline-blue focus:border-blue-300 sm:text-sm sm:leading-5"
						>
							<span
								x-show="!open"
								x-html="value in options ? `<img src='${options[value].country_flag_url}' alt='Flag' class='inline-block mr-2' /><span class='${! (value in options) ? 'text-gray-500' : ''}'>${options[value].native_name}</span>` : placeholder"
								class="block truncate"
							></span>

							<input
								x-ref="search"
								x-show="open"
								x-model="search"
								@keydown.enter.stop.prevent="selectOption()"
								@keydown.arrow-up.prevent="focusPreviousOption()"
								@keydown.arrow-down.prevent="focusNextOption()"
								type="search"
								class="w-full h-full form-control focus:outline-none -my-2"
							/>

							<span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
								<svg class="w-5 h-5 text-gray-400" viewBox="0 0 20 20" fill="none"
									stroke="currentColor">
									<path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round"
											stroke-linejoin="round"></path>
								</svg>
							</span>
						</div>
				</span>

			<div
				x-show="open"
				x-transition:leave="transition ease-in duration-100"
				x-transition:leave-start="opacity-100"
				x-transition:leave-end="opacity-0"
				x-cloak
				class="absolute z-10 w-full mt-1 bg-white rounded-md shadow-lg"
			>
				<ul
					x-ref="listbox"
					@keydown.enter.stop.prevent="selectOption()"
					@keydown.arrow-up.prevent="focusPreviousOption()"
					@keydown.arrow-down.prevent="focusNextOption()"
					role="listbox"
					:aria-activedescendant="focusedOptionIndex ? name + 'Option' + focusedOptionIndex : null"
					tabindex="-1"
					class="py-1 overflow-auto text-base leading-6 rounded-md shadow-xs max-h-60 focus:outline-none sm:text-sm sm:leading-5"
				>
					<template x-for="(key, index) in Object.keys(options)" :key="index">
						<li
							:id="name + 'Option' + focusedOptionIndex"
							@click="selectOption(); selectedLang = key"
							@mouseenter="focusedOptionIndex = index"
							@mouseleave="focusedOptionIndex = null"
							role="option"
							:aria-selected="focusedOptionIndex === index"
							:class="{ 'text-white bg-indigo-600': index === focusedOptionIndex, 'text-gray-900': index !== focusedOptionIndex }"
							class="relative py-2 pl-3 text-gray-900 cursor-default select-none pr-9"
						>
										<span
											x-html="`<img src='${Object.values(options)[index].country_flag_url}' alt='Flag' class='inline-block mr-2' /><span class='${index === focusedOptionIndex ? 'font-semibold' : 'font-normal'}'>${Object.values(options)[index].native_name}</span>`"
											:class="{ 'font-semibold': index === focusedOptionIndex, 'font-normal': index !== focusedOptionIndex }"
											class="block font-normal truncate"
										></span>

							<span
								x-show="key === value"
								:class="{ 'text-white': index === focusedOptionIndex, 'text-indigo-600': index !== focusedOptionIndex }"
								class="absolute inset-y-0 right-0 flex items-center pr-4 text-indigo-600"
							>
											<svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
												<path fill-rule="evenodd"
														d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
														clip-rule="evenodd"/>
											</svg>
										</span>
						</li>
					</template>

					<div
						x-show="! Object.keys(options).length"
						x-text="emptyOptionsMessage"
						class="px-3 py-2 text-gray-900 cursor-default select-none"></div>
				</ul>
			</div>
		</div>
	<?php endif; ?>

	<div class="col-span-2">
		<?php foreach ( $data->option_keys as $axeptio_index => $axeptio_key ) : ?>
			<div class="max-w-sm" x-show="selectedLang === '<?php echo esc_attr( $axeptio_index ); ?>'">
				<input type="hidden" x-model="selectedOption.<?php echo esc_attr( $axeptio_key ); ?>" id="xpwp_version">
								<select
					name="axeptio_settings[<?php echo esc_attr( $axeptio_key ); ?>]"
					class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:max-w-xs sm:text-sm sm:leading-6"
				>
					<option value=""><?php esc_html_e( 'Dynamic (Axeptio based)', 'axeptio-wordpress-plugin' ); ?></option>
					<template x-for="option in options" :key="option.value">
						<option
							:value="option.value"
							x-text="option.text"
							:selected="option.value === selectedOption.<?php echo esc_attr( $axeptio_key ); ?>"
						></option>
					</template>
				</select>
			</div>
		<?php endforeach; ?>
	</div>
</div>
