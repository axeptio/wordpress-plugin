<?php
use Axeptio\Plugin\Models\I18n;

$is_multilingual     = I18n::has_multilangual();
$axeptio_languages   = array();
$default_lang        = '';

if ( $is_multilingual ) {
	$axeptio_languages = I18n::get_languages();
	$default_lang      = array_key_first( $axeptio_languages );
} else {
	$default_lang      = 'default';
	$axeptio_languages = array(
		'default' => array(
			'language_code' => 'default',
			'native_name'   => __( 'Default', 'axeptio-wordpress-plugin' ),
		),
	);
}
?>
<div x-show="editOpen"
	x-cloak
	x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
	x-transition:enter-start="translate-x-full"
	x-transition:enter-end="translate-x-0"
	x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
	x-transition:leave-start="translate-x-0"
	x-transition:leave-end="translate-x-full"
	class="fixed pointer-events-auto w-screen max-w-md top-[46px] md:top-[32px] right-0 bottom-0 z-10"
	x-description="Slide-over panel, show/hide based on slide-over state."
	@click.away="closePanel()"
>
	<div
		x-ref="scrollContainer"
		x-init="$watch('editOpen', value => { if (value) setTimeout(() => $el.scrollTop = 0, 100) })"
		class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl z-50"
	>
		<div class="px-4 py-6 sm:px-6">
			<div class="flex items-start justify-between">
				<h2 id="slide-over-heading" class="text-base font-semibold leading-6 text-gray-900" x-text="editedPlugin.Name"></h2>
				<div class="ml-3 flex h-7 items-center">
					<button type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-amber-400" @click="closePanel()">
						<span class="sr-only">
							<?php esc_html_e( 'Close', 'axeptio-wordpress-plugin' ); ?>
						</span>
						<svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
							<path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
						</svg>
					</button>
				</div>
			</div>

			<div class="border-b border-gray-200 mb-4">
				<nav class="-mb-px flex" aria-label="Tabs">
					<button
						@click="setActive(1)"
						:class="isActive(1) ? 'border-amber-400 text-amber-600': 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
						class="w-1/2 border-b-2 py-4 px-1 text-center text-sm font-medium"
					>
						<?php esc_html_e( 'Informations', 'axeptio-wordpress-plugin' ); ?>
					</button>
					<button
						@click="setActive(2)"
						:class="isActive(2) ? 'border-amber-400 text-amber-600': 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700'"
						class="w-1/2 border-b-2 py-4 px-1 text-center text-sm font-medium"
					>
						<?php esc_html_e( 'Hooks and shortcodes', 'axeptio-wordpress-plugin' ); ?>
					</button>
				</nav>
			</div>

			<div
				class="space-y-6"
				x-show="isActive(1)"
				x-transition:enter="ease-out duration-200"
				x-transition:enter-start="opacity-0"
				x-transition:enter-end="opacity-100"
				x-transition:leave="ease-in duration-150"
				x-transition:leave-start="opacity-100"
				x-transition:leave-end="opacity-0"
			>

				<div>
					<label for="vendor-title" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'Title', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<div class="mt-2">
						<input
							x-model="editedPlugin.Metas.vendor_title"
							type="text"
							id="vendor-title"
							autocomplete="street-address"
							class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:text-sm sm:leading-6"
							:placeholder="editedPlugin?.Metas?.Merged?.vendor_title ?? editedPlugin.Name"
						>
					</div>
				</div>

				<div>
					<label for="vendor-short-description" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'Short description', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<div class="mt-2">
						<textarea
							x-model="editedPlugin.Metas.vendor_shortDescription"
							rows="7"
							id="vendor-short-description"
							class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:text-sm sm:leading-6"
							:placeholder="editedPlugin?.Metas?.Merged?.vendor_shortDescription ?? editedPlugin.Description"
						></textarea>
					</div>
				</div>

				<div>
					<label for="vendor-long-description" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'Long description', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<div class="mt-2">
						<textarea
							x-model="editedPlugin.Metas.vendor_longDescription"
							rows="3" id="vendor-long-description"
							class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:text-sm sm:leading-6"
							:placeholder="editedPlugin?.Metas?.Merged?.vendor_longDescription ?? ''"
						></textarea>
					</div>
				</div>

				<div>
					<label for="vendor-policy-url" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'Policy URL', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<div class="mt-2">
						<input
							x-model="editedPlugin.Metas.vendor_policyUrl"
							type="url"
							id="vendor-policy-url"
							autocomplete="street-address"
							class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:text-sm sm:leading-6"
							:placeholder="editedPlugin?.Metas?.Merged?.vendor_policyUrl ?? editedPlugin.PluginURI"
						>
					</div>
				</div>

				<div>
					<label for="photo" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'Icon', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<input type="hidden" x-model="editedPlugin.Metas.vendor_image" readonly>
					<div class="mt-2 flex items-center gap-x-3">
						<div x-show="!editedPlugin.Metas.vendor_image && !editedPlugin?.Metas?.Merged?.vendor_image">
							<svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
								<path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd"></path>
							</svg>
						</div>
						<img x-show="editedPlugin.Metas.vendor_image || editedPlugin?.Metas?.Merged?.vendor_image" class="h-12 w-12 aspect-[1/1]" :src=" editedPlugin.Metas.vendor_image || editedPlugin?.Metas?.Merged?.vendor_image" alt="<?php esc_attr_e( 'Image preview', 'axeptio-wordpress-plugin' ); ?>">
						<button
							@click="openMediaSelector"
							type="button"
							class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50"
							x-text="editedPlugin.Metas.vendor_image || editedPlugin?.Metas?.Merged?.vendor_image ? '<?php esc_attr_e( 'Edit', 'axeptio-wordpress-plugin' ); ?>' : '<?php esc_attr_e( 'Select', 'axeptio-wordpress-plugin' ); ?>'"
						>
						</button>
					</div>
				</div>
			</div>

			<div
				class="space-y-6"
				x-show="isActive(2)"
				x-transition:enter="ease-out duration-200"
				x-transition:enter-start="opacity-0"
				x-transition:enter-end="opacity-100"
				x-transition:leave="ease-in duration-150"
				x-transition:leave-start="opacity-100"
				x-transition:leave-end="opacity-0"
			>
				<div>
					<label for="wp-filter-mode" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'Hook to be filtered', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<select x-model="editedPlugin.Metas.wp_filter_mode" id="wp-filter-mode" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-amber-400 sm:text-sm sm:leading-6">
						<template x-for="hookMode in hookModes" :key="hookMode.value">
							<option
								:value="hookMode.value"
								x-text="hookMode.text"
								:selected="hookMode.value === editedPlugin.Metas.wp_filter_mode"
							></option>
						</template>
					</select>
				</div>

				<div x-show="editedPlugin.Metas.wp_filter_mode == 'blacklist' || editedPlugin.Metas.wp_filter_mode == 'whitelist'">
					<label for="about" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'List of hooks (action or filter)', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<div class="mt-2">
						<?php
						\Axeptio\Plugin\get_template_part(
							'admin/plugins/fields/repeater',
							array(
								'name'        => 'wp_filter_list',
								'add_item'    => __( 'Add a hook', 'axeptio-wordpress-plugin' ),
								'delete_item' => __( 'Delete', 'axeptio-wordpress-plugin' ),
							)
							);
						?>
					</div>
					<p class="mt-3 text-xs leading-4 text-gray-600">
						<?php
							echo wp_kses(
								__(
									'Determines if the Axeptio WordPress plugin will intercept and block the <code>$wp_filters</code>
									that have been added by the selected Plugin. Hooks are very common within 3rd party plugins
									as they are used to interacting with the page code, like adding script or stylesheet tags, edit the
									content of a section of the template, etc.',
									'axeptio-wordpress-plugin'
								),
								array(
									'code' => array(),
								),
							);
							?>
					</p>
				</div>

				<div>
					<label for="shortcode-tags-mode" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'Shortcodes to be filtered', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<select x-model="editedPlugin.Metas.shortcode_tags_mode" id="shortcodes-tags-mode" class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-amber-400 sm:text-sm sm:leading-6">
						<template x-for="shortcodeTagsMode in shortcodeTagsModes" :key="shortcodeTagsMode.value">
							<option
								:value="shortcodeTagsMode.value"
								x-text="shortcodeTagsMode.text"
								:selected="shortcodeTagsMode.value === editedPlugin.Metas.shortcode_tags_mode"
							></option>
						</template>
					</select>
				</div>

				<div x-show="editedPlugin.Metas.shortcode_tags_mode == 'blacklist' || editedPlugin.Metas.shortcode_tags_mode == 'whitelist'">
					<label for="shortcode-tags-list" class="block text-sm font-medium leading-6 text-gray-900">
						<?php esc_html_e( 'List of shortcode Tags', 'axeptio-wordpress-plugin' ); ?>
					</label>
					<div class="mt-2">
						<?php
						\Axeptio\Plugin\get_template_part(
							'admin/plugins/fields/repeater',
							array(
								'name'        => 'shortcode_tags_list',
								'add_item'    => __( 'Add a shortcode', 'axeptio-wordpress-plugin' ),
								'delete_item' => __( 'Delete', 'axeptio-wordpress-plugin' ),
								'placeholder' => __( 'Example tag : axeptio --forceReload', 'axeptio-wordpress-plugin' ),
							)
							);
						?>
					</div>
					<p class="mt-3 text-xs leading-4 text-gray-600">
						<?php
						echo wp_kses(
								__(
									'Some plugins declare <code>[shortcodes]</code> that you can use in the Post editor.
									These shortcodes are commonly used to embed 3rd party content, like videos or maps. If you
									think the shortcodes provided by the selected plugin are going to load a resource from another
									website, you should probably block them preemptively.',
									'axeptio-wordpress-plugin'
								),
								array(
									'code' => array(),
								),
							);
						?>
					</p>
				</div>

				<div class="border-t border-gray-200 pt-6 mt-6">
					<h3 class="text-sm font-semibold leading-6 text-gray-900 mb-4">
						<?php esc_html_e( 'Consent Banner', 'axeptio-wordpress-plugin' ); ?>
					</h3>

					<?php if ( $is_multilingual ) : ?>
					<div class="mb-4" x-init="selectedPlaceholderLang = '<?php echo esc_attr( $default_lang ); ?>'">
						<?php
						\Axeptio\Plugin\get_template_part(
							'admin/common/fields/select-languages',
							array(
								'label'     => __( 'Language', 'axeptio-wordpress-plugin' ),
								'group'     => '',
								'name'      => 'placeholder_lang',
								'id'        => 'xpwp_placeholder_lang',
								'languages' => $axeptio_languages,
								'value'     => $default_lang,
							)
						);
						?>
					</div>
					<?php endif; ?>

					<?php foreach ( $axeptio_languages as $lang_code => $lang ) : ?>
					<div class="space-y-4" x-show="selectedPlaceholderLang === '<?php echo esc_attr( $lang['language_code'] ); ?>'">
						<div>
							<label for="shortcode-placeholder-title-<?php echo esc_attr( $lang['language_code'] ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
								<?php esc_html_e( 'Shortcode placeholder title', 'axeptio-wordpress-plugin' ); ?>
							</label>
							<div class="mt-2">
								<input
									:value="getLocalizedMeta('shortcode_placeholder_title', '<?php echo esc_attr( $lang['language_code'] ); ?>')"
									@input="setLocalizedMeta('shortcode_placeholder_title', '<?php echo esc_attr( $lang['language_code'] ); ?>', $event.target.value)"
									type="text"
									id="shortcode-placeholder-title-<?php echo esc_attr( $lang['language_code'] ); ?>"
									class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:text-sm sm:leading-6"
									placeholder="<?php echo esc_attr__( 'Oops, content not available at the moment!', 'axeptio-wordpress-plugin' ); ?>"
								>
							</div>
							<p class="mt-2 text-xs leading-4 text-gray-600">
								<?php esc_html_e( 'Custom title displayed when shortcode content is blocked. If left empty, the default title will be used.', 'axeptio-wordpress-plugin' ); ?>
							</p>
						</div>

						<div>
							<label for="shortcode-placeholder-description-<?php echo esc_attr( $lang['language_code'] ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
								<?php esc_html_e( 'Shortcode placeholder description', 'axeptio-wordpress-plugin' ); ?>
							</label>
							<div class="mt-2">
								<?php
								$default_placeholder_text = sprintf(
									__( 'This content is blocked because we take the protection of your data very seriously. If you wish to unblock it, it\'s very simple: go to our cookie consent widget, give your approval for the "%s" extension. And voila, you\'re all set!', 'axeptio-wordpress-plugin' ),
									'{plugin_name}'
								);
								?>
								<textarea
									:value="getLocalizedMeta('shortcode_placeholder_description', '<?php echo esc_attr( $lang['language_code'] ); ?>')"
									@input="setLocalizedMeta('shortcode_placeholder_description', '<?php echo esc_attr( $lang['language_code'] ); ?>', $event.target.value)"
									rows="5"
									id="shortcode-placeholder-description-<?php echo esc_attr( $lang['language_code'] ); ?>"
									class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:text-sm sm:leading-6"
									placeholder="<?php echo esc_attr( $default_placeholder_text ); ?>"
								></textarea>
							</div>
							<p class="mt-2 text-xs leading-4 text-gray-600">
								<?php esc_html_e( 'Custom description displayed when shortcode content is blocked. If left empty, the default description will be used. You can use {plugin_name} as a placeholder for the plugin title.', 'axeptio-wordpress-plugin' ); ?>
							</p>
						</div>

						<div>
							<label for="shortcode-placeholder-button-text-<?php echo esc_attr( $lang['language_code'] ); ?>" class="block text-sm font-medium leading-6 text-gray-900">
								<?php esc_html_e( 'Shortcode placeholder button text', 'axeptio-wordpress-plugin' ); ?>
							</label>
							<div class="mt-2">
								<input
									:value="getLocalizedMeta('shortcode_placeholder_button_text', '<?php echo esc_attr( $lang['language_code'] ); ?>')"
									@input="setLocalizedMeta('shortcode_placeholder_button_text', '<?php echo esc_attr( $lang['language_code'] ); ?>', $event.target.value)"
									type="text"
									id="shortcode-placeholder-button-text-<?php echo esc_attr( $lang['language_code'] ); ?>"
									class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:text-sm sm:leading-6"
									placeholder="<?php echo esc_attr__( 'I accept this service', 'axeptio-wordpress-plugin' ); ?>"
								>
							</div>
							<p class="mt-2 text-xs leading-4 text-gray-600">
								<?php esc_html_e( 'Custom button text displayed on the placeholder. If left empty, the default text will be used.', 'axeptio-wordpress-plugin' ); ?>
							</p>
						</div>
					</div>
					<?php endforeach; ?>

					<div class="mt-4 pt-4 border-t border-gray-100">
						<?php
						\Axeptio\Plugin\get_template_part(
							'admin/common/fields/toggle',
							array(
								'id'           => 'xpwp_shortcode_placeholder_hide_decoration',
								'name'         => 'shortcode_placeholder_hide_decoration',
								'alpine_state' => 'editedPlugin.Metas.shortcode_placeholder_hide_decoration',
								'label'        => __( 'Hide decoration', 'axeptio-wordpress-plugin' ),
								'description'  => __( 'Hide the cookie icon and background shape on the placeholder.', 'axeptio-wordpress-plugin' ),
								'checked'      => false,
							)
						);
						?>
					</div>
				</div>
			</div>

			<div class="mt-6">
				<div>
					<button @click.prevent="updatePlugin(editedPlugin)"
							type="button"
							class="flex w-full justify-center rounded-md bg-amber-400 px-3.5 py-2.5 text-sm border-0
									font-semibold text-gray-900 shadow-sm hover:bg-gray-900 hover:text-amber-400
									focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2
									focus-visible:outline-white transition-colors duration-300">
						<span
							class="inline-flex"
							x-show="isSaving"
						>
							<svg
								class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
								xmlns="http://www.w3.org/2000/svg"
								fill="none"
								viewBox="0 0 24 24"
							>
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
							</svg>
							<?php esc_attr_e( 'Saving...', 'axeptio-wordpress-plugin' ); ?>
						</span>
						<span
							class="inline-flex"
							x-show="!isSaving"
						>
							<?php esc_attr_e( 'Save settings', 'axeptio-wordpress-plugin' ); ?>
						</span>
					</button>
				</div>
				<div class="flex justify-center mt-4" x-show="configurationId !== 'all'">
					<button @click="openDeleteModal()" class="text-sm font-semibold text-rose-600 hover:text-rose-500"><?php esc_attr_e( 'Delete these settings', 'axeptio-wordpress-plugin' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>
