<?php

use Axeptio\Plugin\Admin\Pages\Admin_Callbacks;
use Axeptio\Plugin\Models\Axeptio_Steps;

$is_multilingual = Axeptio\Plugin\Models\i18n::has_multilangual();
$current_lang = [];

if ($is_multilingual) {
	$axeptio_languages = \Axeptio\Plugin\Models\i18n::get_languages();
	$default_lang = array_key_first($axeptio_languages);
} else {
	// Pour les sites monolingues, on utilise la langue courante
	$current_lang = [
		'language_code' => get_locale(),
		'native_name' => __('Default', 'axeptio-wordpress-plugin')
	];
	$axeptio_languages = [$current_lang];
}
?>

<div class="widgetOptions lg:w-4/6 2xl:w-3/4" <?php echo $is_multilingual ? 'x-data="{ selectedLang: \'' . esc_attr($default_lang) . '\' }"' : ''; ?>>
	<?php if ($is_multilingual) : ?>
		<div class="mb-6">
			<?php
			\Axeptio\Plugin\get_template_part(
				'admin/common/fields/select-languages',
				array(
					'label'     => __('Widget language', 'axeptio-wordpress-plugin'),
					'group'     => 'axeptio_settings',
					'name'      => 'widget_title',
					'id'        => 'xpwp_widget_title',
					'languages' => $axeptio_languages,
					'value'     => $default_lang,
				)
			);
			?>

			<?php // Event listener to update the selected language ?>
			<div x-init="
				window.addEventListener('language-changed', (event) => {
					if (event.detail && event.detail.language) {
						selectedLang = event.detail.language;
					} else if (event.detail && event.detail.value) {
						selectedLang = event.detail.value;
					}
				});
			"></div>
		</div>
	<?php endif; ?>

	<?php foreach ($axeptio_languages as $lang) : ?>
		<div class="space-y-6" <?php echo $is_multilingual ? 'x-show="selectedLang === \'' . esc_attr($lang['language_code']) . '\'"' : ''; ?>>
			<?php Admin_Callbacks::render_widget_fields($lang['language_code']); ?>
		</div>
	<?php endforeach; ?>
</div>
