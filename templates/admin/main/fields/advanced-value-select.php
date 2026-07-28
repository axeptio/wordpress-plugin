<?php defined( 'ABSPATH' ) || exit; ?>
<div class="relative">
	<button
		type="button"
		@click="toggleSelect(<?php echo esc_attr( $data->open ); ?>)"
		:aria-expanded="isOpen(<?php echo esc_attr( $data->open ); ?>)"
		class="relative w-full cursor-pointer rounded-lg bg-white py-2.5 pl-3.5 pr-10 text-left text-sm text-gray-900 ring-1 ring-inset ring-gray-300 transition hover:ring-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400"
	>
		<span class="block truncate" x-text="<?php echo esc_attr( $data->current ); ?>"></span>
		<span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
			<img src="<?php echo esc_url( $data->icons . 'selector.svg' ); ?>" class="size-4" alt="">
		</span>
	</button>
	<?php if ( ! empty( $data->hidden_name ) ) : ?>
		<input type="hidden" :name="<?php echo esc_attr( $data->hidden_name ); ?>" :value="<?php echo esc_attr( $data->hidden_value ); ?>">
	<?php endif; ?>
	<ul
		x-show="isOpen(<?php echo esc_attr( $data->open ); ?>)"
		x-cloak
		@click.away="isOpen(<?php echo esc_attr( $data->open ); ?>) && closeSelect()"
		x-transition:enter="transition ease-out duration-150"
		x-transition:enter-start="opacity-0 -translate-y-1"
		x-transition:enter-end="opacity-100 translate-y-0"
		x-transition:leave="transition ease-in duration-100"
		x-transition:leave-start="opacity-100"
		x-transition:leave-end="opacity-0"
		class="absolute z-20 mt-1.5 w-full overflow-auto rounded-xl bg-white p-1.5 text-sm shadow-lg ring-1 ring-gray-200"
	>
		<template x-for="option in <?php echo esc_attr( $data->options ); ?>" :key="option.value">
			<li
				@click="<?php echo esc_attr( $data->on_select ); ?>"
				class="cursor-pointer rounded-lg px-3 py-2.5 mb-0 flex items-center justify-between hover:bg-gray-100"
			>
				<span x-text="option.label" :class="<?php echo esc_attr( $data->selected ); ?> ? 'font-medium text-amber-600' : 'text-gray-900'"></span>
				<img x-show="<?php echo esc_attr( $data->selected ); ?>" src="<?php echo esc_url( $data->icons . 'check.svg' ); ?>" class="size-4" alt="">
			</li>
		</template>
	</ul>
</div>
