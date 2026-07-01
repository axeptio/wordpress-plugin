<?php defined( 'ABSPATH' ) || exit; ?>
<div class="mb-6">
	<div class="sm:hidden">
		<label for="tabs" class="sr-only">
			<?php esc_html_e( 'Select a tab', 'axeptio-sdk-integration' ); ?>
		</label>
		<select x-model="currentTab" class="block w-full rounded-md border-gray-300 focus:border-amber-500 focus:ring-amber-500">
			<?php foreach ( $data->tab_items as $axeptio_tab_item_key => $axeptio_tab_item ) : ?>
				<option value="<?php echo esc_attr( $axeptio_tab_item_key ); ?>"><?php echo esc_html( $axeptio_tab_item ); ?></option>
			<?php endforeach; ?>
		</select>
	</div>
	<div class="hidden sm:block">
		<nav
			class="relative flex gap-4"
			aria-label="Tabs"
			x-data="tabsPill"
			@resize.window.debounce.150ms="move(false)"
		>
			<div
				class="axeptio-tab-pill absolute inset-0 bg-amber-400"
				aria-hidden="true"
				x-cloak
				:style="'clip-path: ' + clip"
			></div>
			<?php foreach ( $data->tab_items as $axeptio_tab_item_key => $axeptio_tab_item ) : ?>
				<button type="button" data-tab="<?php echo esc_attr( $axeptio_tab_item_key ); ?>" @click="currentTab = '<?php echo esc_attr( $axeptio_tab_item_key ); ?>'" aria-pressed="<?php echo $axeptio_tab_item_key === array_key_first( $data->tab_items ) ? 'true' : 'false'; ?>" :aria-pressed="currentTab === '<?php echo esc_attr( $axeptio_tab_item_key ); ?>'" class="relative z-10 rounded-full px-3.5 py-2.5 text-sm font-semibold text-gray-900 transition-colors hover:text-gray-700 <?php echo $axeptio_tab_item_key === array_key_first( $data->tab_items ) ? 'axeptio-tab--initial' : ''; ?>">
					<?php echo esc_html( $axeptio_tab_item ); ?>
				</button>
			<?php endforeach; ?>
		</nav>
	</div>
</div>
