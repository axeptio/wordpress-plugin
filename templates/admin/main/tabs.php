<div class="mb-6">
	<div class="sm:hidden">
		<label for="tabs" class="sr-only">
			<?php esc_html_e( 'Select a tab', 'axeptio-wordpress-plugin' ); ?>
		</label>
		<select x-model="currentTab" class="block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
			<?php foreach ( $data->tab_items as $axeptio_tab_item_key => $axeptio_tab_item ) : ?>
				<option value="<?php echo esc_attr( $axeptio_tab_item_key ); ?>"><?php echo esc_html( $axeptio_tab_item ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="hidden sm:block">
		<nav class="flex space-x-4" aria-label="Tabs">
			<?php foreach ( $data->tab_items as $axeptio_tab_item_key => $axeptio_tab_item ) : ?>
				<button type="button" @click="currentTab = '<?php echo esc_attr( $axeptio_tab_item_key ); ?>'" class="text-gray-900 hover:text-gray-700 rounded-md px-3 py-2 text-sm font-medium" :class="{ 'bg-amber-400/20': currentTab === '<?php echo esc_attr( $axeptio_tab_item_key ); ?>' }">
					<?php echo esc_html( $axeptio_tab_item ); ?>
				</button>
			<?php endforeach; ?>
		</nav>
	</div>
</div>
