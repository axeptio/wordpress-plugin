<?php

use Axeptio\Plugin\Admin\Pages\Admin_Callbacks;
use Axeptio\Plugin\Models\Axeptio_Steps;

$axeptio_is_multilingual = Axeptio\Plugin\Models\i18n::has_multilangual();
$axeptio_current_lang    = array();

if ( $axeptio_is_multilingual ) {
	$axeptio_languages    = \Axeptio\Plugin\Models\i18n::get_languages();
	$axeptio_default_lang = array_key_first( $axeptio_languages );
} else {
	// For monolingual sites, we use the current language.
	$axeptio_current_lang = array(
		'language_code' => get_locale(),
		'native_name'   => __( 'Default', 'axeptio-sdk-integration' ),
	);
	$axeptio_languages    = array( $axeptio_current_lang );
}
?>

<div class="widgetOptions lg:w-4/6 2xl:w-3/4"
	<?php if ( $axeptio_is_multilingual ) : ?>
		x-data="{ selectedLang: '<?php echo esc_attr( $axeptio_default_lang ); ?>' }"
		@language-changed.window="selectedLang = $event.detail.language || $event.detail.value"
	<?php endif; ?>
>
	<?php if ( $axeptio_is_multilingual ) : ?>
		<div class="mb-6">
			<?php
			// UI-only filter: an empty 'group' keeps the selector out of the saved settings.
			\Axeptio\Plugin\get_template_part(
				'admin/common/fields/select-languages',
				array(
					'label'     => __( 'Widget language', 'axeptio-wordpress-plugin' ),
					'group'     => '',
					'name'      => 'widget_lang',
					'id'        => 'xpwp_widget_lang',
					'languages' => $axeptio_languages,
					'value'     => $axeptio_default_lang,
				)
			);
			?>
		</div>
	<?php endif; ?>

	<?php foreach ( $axeptio_languages as $axeptio_lang ) : ?>
		<div class="space-y-6"
			<?php if ( $axeptio_is_multilingual ) : ?>
				x-show="selectedLang === '<?php echo esc_attr( $axeptio_lang['language_code'] ); ?>'"
				x-cloak
			<?php endif; ?>
		>
			<?php Admin_Callbacks::render_widget_fields( $axeptio_lang['language_code'] ); ?>
		</div>
	<?php endforeach; ?>
</div>
