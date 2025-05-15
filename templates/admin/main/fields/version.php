<?php

use Axeptio\Plugin\Models\Project_Versions;
use Axeptio\Plugin\Models\Settings;

?>
<div class="grid grid-cols-3 gap-4" x-data="{ selectedLang: '0' }">
	<?php
	$axeptio_option_list = json_decode( Settings::get_option( 'xpwp_version_options', '', false ) );
	?>
	<?php if ( Axeptio\Plugin\Models\i18n::has_multilangual() && $axeptio_option_list ) : ?>
		<?php
			$axeptio_languages          = \Axeptio\Plugin\Models\i18n::get_languages();
			$axeptio_localized_versions = Project_Versions::get_localized_versions();
		?>
		<p class="block text-sm font-medium leading-6 text-gray-900">
			<?php esc_html_e( 'Configured languages', 'axeptio-wordpress-plugin' ); ?>
		</p>
		<div role="list" class="-mt-2 divide-y divide-gray-300 col-span-3 border border-grey-100 px-2">
			<?php foreach ( $axeptio_languages as $axeptio_language_key => $axeptio_language ) : ?>
				<?php
					$axeptio_option = Settings::get_option( 'version' . $axeptio_language['option_key_suffix'], '' );
				?>
				<div class="flex justify-between gap-x-6 py-2">
					<div class="flex min-w-0 gap-x-4 items-center">
						<img src="<?php echo esc_attr( $axeptio_language['country_flag_url'] ); ?>" alt="Flag" class="flex-none mr-2 w-[16px] h-[11px]">
						<div class="min-w-0 flex-auto">
							<p class="text-sm font-semibold leading-6 text-gray-900">
								<?php echo esc_html( $axeptio_language['native_name'] ); ?>
							</p>
						</div>
					</div>
					<div class="hidden shrink-0 sm:flex sm:flex-col sm:items-end">
						<select
							name="axeptio_settings[<?php echo esc_attr( $axeptio_localized_versions[ $axeptio_language_key ] ); ?>]"
							class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-amber-400 sm:max-w-xs sm:text-sm sm:leading-6"
						>
							<option value=""><?php esc_html_e( 'Dynamic (Axeptio based)', 'axeptio-wordpress-plugin' ); ?></option>
							<template x-for="option in options" :key="option.value">
								<option
									:value="option.value"
									x-text="option.text"
									:selected="option.value === selectedOption.<?php echo esc_attr( $axeptio_localized_versions[ $axeptio_language_key ] ); ?>"
								></option>
							</template>
						</select>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
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
	<?php endif; ?>
	<div class="rounded-md bg-amber-50 p-4 col-span-3" x-show="isHistorizedVersion">
		<div class="flex">
			<div class="flex-shrink-0">
				<svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
					<path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
				</svg>
			</div>
			<div class="ml-3">
				<h3 class="text-sm font-medium text-amber-800">
					<?php esc_html_e( 'Existing project version backup', 'axeptio-wordpress-plugin' ); ?>
				</h3>
				<div class="mt-2 text-sm text-amber-700">
					<p><?php esc_html_e( 'A backup of the version configuration exists for this project, do you want to restore it?', 'axeptio-wordpress-plugin' ); ?></p>
				</div>
				<div class="mt-4">
					<div class="-mx-2 -my-1.5 flex">
						<button type="button" @click="restoreHistorizedVersion()" class="rounded-md bg-amber-50 px-2 py-1.5 text-sm font-medium text-amber-800 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 focus:ring-offset-amber-50">
							<?php esc_html_e( 'Yes, restore it', 'axeptio-wordpress-plugin' ); ?>
						</button>
						<button type="button" @click="isHistorizedVersion = false" class="ml-3 rounded-md bg-amber-50 px-2 py-1.5 text-sm font-medium text-amber-800 hover:bg-amber-100 focus:outline-none focus:ring-2 focus:ring-amber-600 focus:ring-offset-2 focus:ring-offset-amber-50">
							<?php esc_html_e( 'Dismiss', 'axeptio-wordpress-plugin' ); ?>
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
