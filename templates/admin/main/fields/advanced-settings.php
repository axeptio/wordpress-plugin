<?php defined( 'ABSPATH' ) || exit; ?>
<?php $xpwp_icons = XPWP_URL . 'dist/img/icons/'; ?>
<div
	class="mt-4 border-t border-gray-200 pt-6"
	x-data='advancedSettings(<?php echo esc_attr( wp_json_encode( $data->config ) ); ?>)'
	@keydown.escape.window="closeSelect()"
>
	<h4 class="text-base font-semibold text-gray-900">
		<?php esc_html_e( 'Custom SDK settings', 'axeptio-sdk-integration' ); ?>
	</h4>
	<p class="mt-1 text-sm text-gray-500">
		<?php esc_html_e( 'Add extra Axeptio SDK options as key/value pairs — they extend your configuration without touching any code.', 'axeptio-sdk-integration' ); ?>
		<a href="https://support.axeptio.eu/en/articles/274040-advanced-options-and-mode-axeptiosettings" target="_blank" rel="noopener noreferrer" class="font-medium text-amber-600 hover:text-amber-500">
			<?php esc_html_e( 'Learn more', 'axeptio-sdk-integration' ); ?>
		</a>
	</p>

	<div x-ref="rows" class="mt-4 divide-y divide-gray-100">
		<template x-for="(row, index) in rows" :key="row._uid">
			<div class="flex items-start gap-3 py-4">
				<div class="flex-1 min-w-0 space-y-2.5">
					<div class="relative">
						<button
							type="button"
							data-select-trigger
							@click="toggleSelect(row._uid + ':key')"
							:aria-expanded="isOpen(row._uid + ':key')"
							class="relative w-full cursor-pointer rounded-lg bg-white py-2.5 pl-3.5 pr-10 text-left text-sm text-gray-900 ring-1 ring-inset ring-gray-300 transition hover:ring-gray-400 focus:outline-none focus:ring-2 focus:ring-amber-400"
						>
							<span
								class="block truncate"
								:class="keyLabel(row) ? 'text-gray-900' : 'text-gray-400'"
								x-text="keyLabel(row) || '<?php echo esc_js( __( 'Select a setting…', 'axeptio-sdk-integration' ) ); ?>'"
							></span>
							<span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
								<img src="<?php echo esc_url( $xpwp_icons . 'selector.svg' ); ?>" class="size-4" alt="">
							</span>
						</button>

						<input type="hidden" :name="fieldName(index, 'property')" :value="row.property">
						<input type="hidden" :name="fieldName(index, 'type')" :value="row.type">

						<div
							x-show="isOpen(row._uid + ':key')"
							x-cloak
							@click.away="isOpen(row._uid + ':key') && closeSelect()"
							x-transition:enter="transition ease-out duration-150"
							x-transition:enter-start="opacity-0 -translate-y-1"
							x-transition:enter-end="opacity-100 translate-y-0"
							x-transition:leave="transition ease-in duration-100"
							x-transition:leave-start="opacity-100"
							x-transition:leave-end="opacity-0"
							class="absolute z-20 mt-1.5 w-full rounded-xl bg-white shadow-lg ring-1 ring-gray-200"
						>
							<div class="border-b border-gray-100 p-1.5">
								<div class="relative">
									<span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-2.5">
										<img src="<?php echo esc_url( $xpwp_icons . 'search.svg' ); ?>" class="size-4" alt="">
									</span>
									<input
										type="text"
										x-model="keySearch"
										x-effect="isOpen(row._uid + ':key') && $nextTick(() => $el.focus())"
										@keydown.enter.prevent="selectFirst(row)"
										placeholder="<?php echo esc_attr__( 'Search a setting…', 'axeptio-sdk-integration' ); ?>"
										class="block w-full rounded-md border-0 py-1.5 pl-8 pr-2.5 text-sm text-gray-900 ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-amber-400"
									>
								</div>
							</div>

							<ul class="max-h-72 overflow-auto p-1.5">
								<template x-for="option in filteredOptions(row)" :key="option.property">
									<li
										@click="selectProperty(row, option.property)"
										@mouseenter="focusedProperty = option.property"
										:class="focusedProperty === option.property ? 'bg-gray-100' : ''"
										class="cursor-pointer rounded-lg px-3 py-2.5 mb-0"
									>
										<div class="flex items-start justify-between gap-3">
											<div class="min-w-0">
												<span class="block font-medium" :class="row.property === option.property ? 'text-amber-600' : 'text-gray-900'" x-text="option.name"></span>
												<span class="mt-0.5 block text-xs text-gray-500 leading-snug" x-text="option.description" x-show="option.description"></span>
											</div>
											<img x-show="row.property === option.property" src="<?php echo esc_url( $xpwp_icons . 'check.svg' ); ?>" class="mt-0.5 size-4 flex-shrink-0" alt="">
										</div>
									</li>
								</template>
								<li x-show="filteredOptions(row).length === 0" class="px-3 py-6 text-center text-xs text-gray-400">
									<?php esc_html_e( 'No setting found', 'axeptio-sdk-integration' ); ?>
								</li>
							</ul>
						</div>
					</div>

					<p class="text-xs leading-snug text-gray-500" x-show="row.property && descriptionOf(row)" x-cloak x-text="descriptionOf(row)"></p>
					<p class="text-xs leading-snug text-amber-600" x-show="isUnknownSetting(row)" x-cloak>
						<?php esc_html_e( 'This setting is no longer listed by Axeptio. You can keep it or remove it.', 'axeptio-sdk-integration' ); ?>
					</p>

					<div class="min-w-0" x-show="row.property" x-cloak>
						<template x-if="widget(row) === WIDGET_TOGGLE">
							<div class="flex h-10 items-center">
								<button
									type="button"
									role="switch"
									@click="toggleBoolean(row)"
									:aria-checked="(row.value === '1').toString()"
									class="group relative inline-flex h-5 w-10 flex-shrink-0 cursor-pointer items-center justify-center rounded-full focus:outline-none focus:ring-2 focus:ring-amber-400 focus:ring-offset-2"
								>
									<span aria-hidden="true" class="pointer-events-none absolute size-full rounded-md bg-white"></span>
									<span aria-hidden="true" class="bg-gray-200 pointer-events-none absolute mx-auto h-4 w-9 rounded-full transition-colors duration-200 ease-in-out" :class="{ 'bg-amber-400': row.value === '1', 'bg-gray-200': row.value !== '1' }"></span>
									<span aria-hidden="true" class="translate-x-0 pointer-events-none absolute left-0 inline-block size-5 transform rounded-full border border-gray-200 bg-white shadow ring-0 transition-transform duration-200 ease-in-out" :class="{ 'translate-x-5': row.value === '1', 'translate-x-0': row.value !== '1' }"></span>
								</button>
								<span class="ml-3 text-sm font-medium text-gray-700" x-text="row.value === '1' ? i18n.bool.on : i18n.bool.off"></span>
								<input type="hidden" :name="fieldName(index, 'value')" :value="row.value">
							</div>
						</template>

						<template x-if="widget(row) === WIDGET_SELECT">
							<?php
							\Axeptio\Plugin\get_template_part(
								'admin/main/fields/advanced-value-select',
								array(
									'icons'        => $xpwp_icons,
									'open'         => "row._uid + ':val'",
									'current'      => 'labelFor(optionsFor(row), row.value)',
									'options'      => 'optionsFor(row)',
									'on_select'    => 'selectValue(row, option.value)',
									'selected'     => 'row.value === option.value',
									'hidden_name'  => "fieldName(index, 'value')",
									'hidden_value' => 'row.value',
								)
							);
							?>
						</template>

						<template x-if="widget(row) === WIDGET_NUMBER">
							<?php \Axeptio\Plugin\get_template_part( 'admin/main/fields/advanced-value-input', array( 'type' => 'number' ) ); ?>
						</template>

						<template x-if="widget(row) === WIDGET_MODE">
							<div class="space-y-2">
								<?php
								\Axeptio\Plugin\get_template_part(
									'admin/main/fields/advanced-value-select',
									array(
										'icons'     => $xpwp_icons,
										'open'      => "row._uid + ':mode'",
										'current'   => 'labelFor(optionsFor(row), modeOf(row))',
										'options'   => 'optionsFor(row)',
										'on_select' => 'selectMode(row, option.value)',
										'selected'  => 'modeOf(row) === option.value',
									)
								);
								?>
								<template x-if="modeOf(row) === MODE_NUMBER">
									<?php \Axeptio\Plugin\get_template_part( 'admin/main/fields/advanced-value-input', array( 'type' => 'number' ) ); ?>
								</template>
								<template x-if="modeOf(row) !== MODE_NUMBER">
									<input type="hidden" :name="fieldName(index, 'value')" :value="row.value">
								</template>
							</div>
						</template>

						<template x-if="widget(row) === WIDGET_LIST || widget(row) === WIDGET_TEXT">
							<div>
								<?php \Axeptio\Plugin\get_template_part( 'admin/main/fields/advanced-value-input', array( 'type' => 'text' ) ); ?>
								<p class="mt-1.5 text-xs text-gray-500" x-show="widget(row) === WIDGET_LIST">
									<?php esc_html_e( 'Comma-separated domain names, e.g. .example.com, .example.fr', 'axeptio-sdk-integration' ); ?>
								</p>
							</div>
						</template>
					</div>

					<p class="text-xs leading-snug text-red-600" x-show="errorFor(row)" x-cloak x-text="errorFor(row)"></p>
				</div>

				<button
					type="button"
					@click="removeRow(index)"
					class="mt-0.5 flex size-9 flex-shrink-0 items-center justify-center rounded-lg text-gray-400 transition hover:bg-red-50 hover:text-red-500 active:scale-95 focus:outline-none focus:ring-2 focus:ring-red-300"
					spellcheck="false"
				>
					<span class="sr-only"><?php esc_html_e( 'Remove this setting', 'axeptio-sdk-integration' ); ?></span>
					<span class="size-5 bg-current xpwp-icon xpwp-icon-remove" aria-hidden="true"></span>
				</button>
			</div>
		</template>
	</div>

	<button
		type="button"
		@click="addRow()"
		:disabled="! canAddRow()"
		class="mt-4 flex w-full items-center justify-center gap-2 rounded-lg border border-dashed border-gray-300 py-2.5 text-sm font-medium text-gray-600 transition hover:border-amber-400 hover:text-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-400 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:border-gray-300 disabled:hover:text-gray-600"
		spellcheck="false"
	>
		<span class="size-4 bg-current xpwp-icon xpwp-icon-plus" aria-hidden="true"></span>
		<?php esc_html_e( 'Add a setting', 'axeptio-sdk-integration' ); ?>
	</button>
	<p class="mt-2 text-center text-xs text-gray-500" x-show="addRowHint()" x-cloak x-text="addRowHint()"></p>
</div>
