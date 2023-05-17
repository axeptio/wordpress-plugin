<div class="max-h-64 overflow-y-auto overflow-x-hidden -mr-3 scroll-smooth" x-ref="scrollContainer">
	<template x-for="(field, index) in getFields('<?php echo esc_attr( $data->name ); ?>')" :key="index">
		<div class="flex items-center mb-3">
			<input @keyup="updateRepeaterField('<?php echo esc_attr( $data->name ); ?>')" x-init="storeRef('<?php echo esc_attr( $data->name ); ?>', $el, index)" @keydown.enter.prevent="addField('<?php echo esc_attr( $data->name ); ?>', index + 1)" @keydown.backspace="removeFieldAndFocusPrevious('<?php echo esc_attr( $data->name ); ?>', index)" x-model="fields['<?php echo esc_attr( $data->name ); ?>'][index]" type="text" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
			<button @click="removeField('<?php echo esc_attr( $data->name ); ?>', index)" type="button" class="mx-1 text-red-500" spellcheck="false">
				<span class="sr-only"><?php echo esc_html( $data->delete_item ); ?></span>
				<svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					<path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z"></path>
				</svg>
			</button>
		</div>
	</template>
</div>
<button @click="addField('<?php echo esc_attr( $data->name ); ?>')" type="button" class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500" spellcheck="false">
	<span aria-hidden="true">+</span> <?php echo esc_html( $data->add_item ); ?>
</button>
