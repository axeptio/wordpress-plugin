<?php

use Axeptio\Admin;

$value      = isset( $value ) ? $value : [
	'reload_page_after_consent' => 'no'
];
$row_exists = isset( $row_exists ) ? $row_exists : false;

$admin   = Admin::instance();
$plugins = get_plugins();

?>
<form style="max-width: 800px;" method="post">
    <input type="hidden" name="action" value="plugin_configuration"/>
    <table class="form-table">
        <tr>
            <th scope="row"><?= __( 'Plugin', 'axeptio-wordpress-plugin' ) ?></th>
            <td>
                <select name="plugin" id="plugin_select">
                    <option value=""><?= __( 'Select', 'axeptio-wordpress-plugin' ) ?></option>
					<?php
					unset( $plugin );
					foreach ( $plugins as $fileName => $plugin ) {
						$dirname  = dirname( $fileName );
						$selected = $dirname === $value['plugin'] ?
							'selected="selected"' : '';

						echo "
                        <option 
                            value='$dirname'
                            data-title='$plugin[Title]'
                            data-description='$plugin[Description]'
                            data-uri='$plugin[PluginURI]'
                            data-version='$plugin[Version]'
                            data-author='$plugin[Author]'
                            $selected
                        >$plugin[Title]</option>";
					}
					?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row"><label
                        for="cookies_version"><?= __( 'Axeptio Cookies Config', 'axeptio-wordpress-plugin' ) ?></label>
            </th>
            <td>
                <select id="cookies_version" name="cookies_version">
                    <option value=""><?= __( 'Every config', 'axeptio-wordpress-plugin' ) ?></option>
					<?php
					$savedCookiesVersion = get_option( Admin::OPTION_COOKIES_VERSION );
					foreach ( $admin->axeptioConfiguration->cookies as $cookieConfiguration ) {
						$selected = $cookieConfiguration->identifier === $value['axeptio_configuration_id'] ?
							'selected="selected"' : '';

						echo "
                            <option 
                                value='$cookieConfiguration->identifier'
                                data-name='$cookieConfiguration->name'
                                data-identifier='$cookieConfiguration->identifier'
                                data-language='$cookieConfiguration->language'
                                $selected
                            >
                                $cookieConfiguration->title
                            </option>";
					}
					?>
                </select>
                <p class="description cookies_version">
                    <span class="all hidden">
                        <?= __( '<strong>There\'s no cookies configuration selected.</strong>
                        This means this rule will apply to every cookie configurations
                        as long as there\'s not another Plugin setup that
                        specifies the cookies configuration explicitly.', 'axeptio-wordpress-plugin' ) ?>

                    </span>
                    <span class="selected hidden">

                        <strong><?= __( 'You have selected the configuration', 'axeptio-wordpress-plugin' ) ?>
                            <code id="axeptio_configuration_identifier"></code>
                        </strong>.<br/>
                        <?= __( 'This rule will only apply when this very configuration is the one loaded on the website.
                        For reference, this configuration has been associated with the following language code:', 'axeptio-wordpress-plugin' ) ?>
                        <code id="axeptio_configuration_language"></code><?= __( ', and has the following name:', 'axeptio-wordpress-plugin' ) ?>
                        <code id="axeptio_configuration_name"></code>.
                    </span>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="cookie_widget_step_select">
					<?= __( 'Cookie Widget Step', 'axeptio-wordpress-plugin' ) ?>
                </label>
            </th>
            <td>
                <input type="hidden" id="cookie_widget_step" name="cookie_widget_step"
                       value="<?= $value['cookie_widget_step'] ?>"/>
                <select name="cookie_widget_step_select" id="cookie_widget_step_select"
                        data-value="<?= $value['cookie_widget_step'] ?>"></select>
            </td>
        </tr>
        <tr>
            <th scope="row">
				<?= __( 'Wordpress Filters', 'axeptio-wordpress-plugin' ) ?>
            </th>
            <td>

                <fieldset>
                    <label>
                        Intercept
                        <select name="wp_filter_mode" class="select_mode">
                            <option value="none" <?= $value["wp_filter_mode"] == 'none' ? "selected" : "" ?>>
								<?= __( 'nothing', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                            <option value="all" <?= $value["wp_filter_mode"] == 'all' ? "selected" : "" ?>>
								<?= __( 'all filters', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                            <option value="blacklist" <?= $value["wp_filter_mode"] == 'blacklist' ? "selected" : "" ?>>
								<?= __( 'only the following', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                            <option value="whitelist" <?= $value["wp_filter_mode"] == 'all' ? "whitelist" : "" ?>>
								<?= __( 'only those other than', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                        </select>
                    </label>
                    <div class="toggle_list wp_filter_mode <?=
					strpos( $value['shortcode_tags_mode'], "list" ) === false ? 'hidden' : ''
					?>">
                        <label><?= __( 'List of filters', 'axeptio-wordpress-plugin' ) ?></label><br>
                        <textarea class="large-text code"
                                  name="wp_filter_list"><?= isset( $value['wp_filter_list'] ) ? esc_textarea( $value['wp_filter_list'] ) : false ?></textarea>
                    </div>
                    <p class="description">
						<?= __( 'Determines if the Axeptio Wordpress plugin will intercept and block the <code>$wp_filters</code>
                        that have been added by the selected Plugin. Filters are very common within 3rd party plugins
                        as they are used to interacting with the page code, like adding script or stylesheet tags, edit the
                        content of a section of the template, etc.', 'axeptio-wordpress-plugin' ) ?>

                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row"><?= __( 'Shortcode Tags', 'axeptio-wordpress-plugin' ) ?></th>
            <td>
                <fieldset>
                    <label>
						<?= __( 'Intercept', 'axeptio-wordpress-plugin' ) ?>
                        <select name="shortcode_tags_mode" class="select_mode">
                            <option value="none" <?= $value["shortcode_tags_mode"] == 'none' ? "selected" : "" ?>>
								<?= __( 'nothing', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                            <option value="all" <?= $value["shortcode_tags_mode"] == 'all' ? "selected" : "" ?>>
								<?= __( 'all tags', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                            <option value="blacklist" <?= $value["shortcode_tags_mode"] == 'blacklist' ? "selected" : "" ?>>
								<?= __( 'only the following', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                            <option value="whitelist" <?= $value["shortcode_tags_mode"] == 'all' ? "whitelist" : "" ?>>
								<?= __( 'only those other than', 'axeptio-wordpress-plugin' ) ?>
                            </option>
                        </select>
                    </label>
                    <div class="toggle_list shortcode_tags_mode <?=
					strpos( $value['shortcode_tags_mode'], "list" ) === false ? 'hidden' : ''
					?>">
                        <label><?= __( 'List of tags', 'axeptio-wordpress-plugin' ) ?></label><br>
                        <textarea class="large-text code" name="shortcode_tags_list">
                            <?= isset( $value['shortcode_tags_list'] ) ? esc_textarea( $value['shortcode_tags_list'] ) : false ?>
                        </textarea>
                    </div>
                    <p class="description">
						<?= __( 'Some plugins declare <code>[shortcodes]</code> that you can use in the Post editor.
                        These shortcodes are commonly used to embed 3rd party content, like videos or maps. If you
                        think the shortcodes provided by the selected plugin are going to load a resource from another
                        website, you should probably block them preemptively.', 'axeptio-wordpress-plugin' ) ?>

                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
				<?= __( 'Vendor configuration', 'axeptio-wordpress-plugin' ) ?>
            </th>
            <td>
                <div class="form-field">
                    <p>
                        <label for="vendor_title"><?= __( 'Title', 'axeptio-wordpress-plugin' ) ?></label></p>
                    <p>
                        <input type="text" id="vendor_title" name="vendor_title"
                               value="<?= isset( $value['vendor_title'] ) ? $value['vendor_title'] : false ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_shortDescription"><?= __( 'Short description', 'axeptio-wordpress-plugin' ) ?></label>
                    </p>
                    <p>
                        <textarea id="vendor_shortDescription"
                                  name="vendor_shortDescription"
                        ><?= isset( $value['vendor_shortDescription'] ) ? esc_textarea( $value['vendor_shortDescription'] ) : false ?></textarea>
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_longDescription"><?= __( 'Long description', 'axeptio-wordpress-plugin' ) ?></label>
                    </p>
                    <p>
                        <textarea id="vendor_longDescription"
                                  name="vendor_longDescription"><?= isset( $value['vendor_longDescription'] ) ? esc_textarea( $value['vendor_longDescription'] ) : false ?></textarea>
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_policyUrl"><?= __( 'Privacy Policy URL', 'axeptio-wordpress-plugin' ) ?></label>
                    </p>
                    <p>
                        <input type="text" id="vendor_policyUrl" name="vendor_policyUrl"
                               value="<?= isset( $value['vendor_policyUrl'] ) ? $value['vendor_policyUrl'] : false ?>"/>
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_image"><?= __( 'Image', 'axeptio-wordpress-plugin' ) ?></label>
                    </p>
                    <p>
                        <input type="text" id="vendor_image" name="vendor_image"
                               value="<?= isset( $value['vendor_image'] ) ? $value['vendor_image'] : false ?>"/>
                    </p>
                </div>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <p class="submit">
                    <input type="submit"
                           name="submit"
                           id="submit"
                           class="button button-primary"
                           value="<?= __( 'Save Changes', 'axeptio-wordpress-plugin' ) ?>">
                </p>
            </td>
        </tr>
    </table>
</form>
<script>
    var widgetConfigurations = <?= json_encode( Admin::instance()->fetchWidgetConfigurations() ) ?>;
    var axeptioConfiguration = <?= json_encode( Admin::instance()->axeptioConfiguration ) ?>;
</script>
<script>
    jQuery(function ($) {

        var $cookieWidgetStep = $('#cookie_widget_step_select');
        var $cookiesVersion = $('#cookies_version');

        $('.select_mode').on('change', function (e) {
            $('.toggle_list.' + e.target.name).toggleClass('hidden', !(e.target.value.indexOf('list') > -1));
        });

        $('#plugin_select').on('change', function (e) {
            $('#vendor_title').attr({placeholder: e.target.selectedOptions[0].dataset.title})
            $('#vendor_shortDescription').attr({placeholder: e.target.selectedOptions[0].dataset.description})
            $('#vendor_policyUrl').attr({placeholder: e.target.selectedOptions[0].dataset.uri})
        });

        function updateCookiesVersionDescription() {
            var selectedOption = $cookiesVersion[0].selectedOptions[0];
            $('.description.cookies_version .all').toggleClass('hidden', selectedOption.value !== '');
            $('.description.cookies_version .selected').toggleClass('hidden', selectedOption.value === '');
            if (selectedOption.value.value !== '') {
                $('#axeptio_configuration_identifier').text(selectedOption.dataset.identifier)
                $('#axeptio_configuration_name').text(selectedOption.dataset.name)
                $('#axeptio_configuration_language').text(selectedOption.dataset.language)
            }
        }

        function updateCookieWidgetSteps() {
            var selectedOption = $cookiesVersion[0].selectedOptions[0];
            var identifier = selectedOption.value;
            var options = [];

            function getOptionLabel(title, subTitle, topTitle, layout, name) {
                return [topTitle, title, subTitle].filter(function (str) {
                    return !!str
                }).join(' ') + ' (Layout: ' + layout + ', Name: ' + name + ')'
            }

            axeptioConfiguration.cookies.filter(function (cookieConfig) {
                return identifier === cookieConfig.identifier || !identifier;
            }).forEach(function (cookieConfig) {
                cookieConfig.steps.forEach(function (step) {
                    options.push({
                        label: getOptionLabel(step.title, step.subTitle, step.topTitle, step.layout, step.name),
                        value: step.name
                    })
                });
            });

            widgetConfigurations.filter(function (widgetConfig) {
                return widgetConfig.axeptio_configuration_id === '' // widget config is not config scoped
                    || identifier === widgetConfig.axeptio_configuration_id // selected config matches widget config
                    || identifier === '' // no config selected, returning every cookies' configurations
            }).forEach(function (widgetConfig) {
                options.push({
                    label: getOptionLabel(widgetConfig.step_title, widgetConfig.step_subTitle, widgetConfig.step_topTitle, 'wordpress', widgetConfig.step_name),
                    value: widgetConfig.step_name
                });
            });
            $cookieWidgetStep.empty().append(options.map(function (option) {
                return $('<option>')
                    .attr('value', option.value)
                    .attr('selected', option.value === $cookieWidgetStep.attr('data-value'))
                    .text(option.label);
            }));
        }

        $cookieWidgetStep.on('change', function (e) {
            $('#cookie_widget_step').val(e.target.value)
        });

        // On cookies version change, we want to allow our user to select a cookie widget step
        // from the selected cookies version or refer to a widget configuration defined
        // in the WordPress extension's admin panel.
        $cookiesVersion.on('change', function () {
            updateCookiesVersionDescription();
            updateCookieWidgetSteps();
        }).trigger('change');


    })
</script>
