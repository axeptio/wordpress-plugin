<?php defined( 'ABSPATH' ) || exit; ?>
<div
	x-data="SelectComponent()"
	x-init="init(<?php echo esc_attr( wp_json_encode( $data ) ); ?>)"
	class="relative"
	x-cloak
>
	<label :for="state.name" class="block text-sm font-medium leading-6 text-gray-900">
		<?php echo esc_html( $data->label ?? '' ); ?>
		<?php if ( isset( $data->help_url ) ) : ?>
			<a href="<?php echo esc_url( $data->help_url ); ?>" target="_blank">
				<span class="dashicons dashicons-info-outline"></span>
			</a>
		<?php endif ?>
	</label>

	<input type="hidden" :name="state.name || null" x-model="state.value">

	<button
		type="button"
		@click="toggleListbox()"
		@keydown.escape="closeListbox()"
		@keydown.arrow-down.prevent="focusNextOption()"
		@keydown.arrow-up.prevent="focusPreviousOption()"
		@keydown.enter.prevent="selectOption(state.focusedOptionIndex)"
		class="relative w-full mt-2 cursor-default rounded-md bg-white py-1.5 pl-3 pr-10 text-left text-gray-900
				shadow-sm ring-1 ring-inset ring-gray-300 focus:outline-none focus:ring-2 focus:ring-amber-400
				sm:text-sm sm:leading-6"
	>
		<span x-html="getSelectedLabel()" class="flex items-center gap-x-2 truncate"></span>
		<span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
			<svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
				<path fill-rule="evenodd" d="M10 3a.75.75 0 01.55.24l3.25 3.5a.75.75 0 11-1.1 1.02L10 4.852 7.3 7.76a.75.75 0 01-1.1-1.02l3.25-3.5A.75.75 0 0110 3zm-3.76 9.2a.75.75 0 011.06.04l2.7 2.908 2.7-2.908a.75.75 0 111.1 1.02l-3.25 3.5a.75.75 0 01-1.1 0l-3.25-3.5a.75.75 0 01.04-1.06z" clip-rule="evenodd" />
			</svg>
		</span>
	</button>

	<ul
		x-show="state.open"
		x-ref="listbox"
		@click.away="closeListbox()"
		class="absolute z-10 mt-1 p-2 max-h-60 w-full overflow-auto rounded-md bg-white text-base sm:text-sm
				shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
		x-transition:leave="transition ease-in duration-100"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
	>
		<template x-for="(option, index) in state.options" :key="option.value">
			<li
				@click="selectOption(index)"
				@mouseenter="state.focusedOptionIndex = index"
				:class="{
					'bg-amber-400 text-white rounded': state.focusedOptionIndex === index,
					'text-gray-900': state.focusedOptionIndex !== index
				}"
				class="relative cursor-default select-none pl-3 pr-9 py-2 space-x-2 mb-0 hover:bg-amber-400 hover:text-white cursor-pointer flex items-center"
			>
				<img :src="option.flag_url" :alt="option.label" class="w-5 h-4 rounded" />
				<span x-text="option.label" :class="{ 'font-semibold': state.value === option.value }" class="block truncate"></span>

				<span
					x-show="state.value === option.value"
					:class="{ 'text-white': state.focusedOptionIndex === index }"
					class="absolute inset-y-0 right-0 flex items-center pr-4 text-amber-400"
				>
					<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
						<path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
					</svg>
				</span>
			</li>
		</template>
	</ul>
</div>
