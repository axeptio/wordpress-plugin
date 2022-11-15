<?php

use Axeptio\Admin;

$value = $value ?? [
	'reload_page_after_consent' => 'no'
];
$row_exists = $row_exists ?? false;

$admin = Admin::instance();
$plugins = get_plugins();

?>
<form style="max-width: 800px;" method="post">
    <input type="hidden" name="action" value="plugin_configuration" />
    <table class="form-table">
        <tr>
            <th scope="row">Plugin</th>
            <td>
                <select name="plugin" id="plugin_select">
                    <option value="">Select</option>
					<?php
					unset( $plugin );
					foreach ( $plugins as $fileName => $plugin ) {
						$dirname = dirname( $fileName );
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
            <th scope="row"><label for="cookies_version">Axeptio Cookies Config</label></th>
            <td>
                <select id="cookies_version" name="cookies_version">
                    <option value="">Every config</option>
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
                        <strong>There's no cookies configuration selected.</strong>
                        This means this rule will apply to every cookie configurations
                        as long as there's not another Plugin setup that
                        specifies the cookies configuration explicitly.
                    </span>
                    <span class="selected hidden">
                        <strong>You have selected the configuration
                            <code id="axeptio_configuration_identifier"></code>
                        </strong>.<br/>
                        This rule will only apply when this very configuration is the one loaded on the website.
                        For reference, this configuration has been associated with the following language code:
                        <code id="axeptio_configuration_language"></code>, and has the following name:
                        <code id="axeptio_configuration_name"></code>.
                    </span>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row">
                Wordpress Filters
            </th>
            <td>

                <fieldset>
                    <label>
                        Intercept
                        <select name="wp_filter_mode" class="select_mode">
                            <option value="none" <?= $value["wp_filter_mode"] == 'none' ? "selected" : "" ?>>
                                nothing
                            </option>
                            <option value="all" <?= $value["wp_filter_mode"] == 'all' ? "selected" : "" ?>>
                                all filters
                            </option>
                            <option value="blacklist" <?= $value["wp_filter_mode"] == 'blacklist' ? "selected" : "" ?>>
                                only the following
                            </option>
                            <option value="whitelist" <?= $value["wp_filter_mode"] == 'all' ? "whitelist" : "" ?>>
                                only those other than
                            </option>
                        </select>
                    </label>
                    <div class="toggle_list wp_filter_mode <?=
					strpos( $value['shortcode_tags_mode'], "list" ) === false ? 'hidden' : ''
					?>">
                        <label>List of filters</label><br>
                        <textarea class="large-text code"
                                  name="wp_filter_list"><?= isset($value['wp_filter_list']) ? $value['wp_filter_list'] : false ?></textarea>
                    </div>
                    <p class="description">
                        Determines if the Axeptio Wordpress plugin will intercept and block the <code>$wp_filters</code>
                        that have been added by the selected Plugin. Filters are very common within 3rd party plugins
                        as they are used to interact with the page code, like adding script or stylesheet tags, edit the
                        content of a section of the template, etc.
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">Shortcode Tags</th>
            <td>
                <fieldset>
                    <label>
                        Intercept
                        <select name="shortcode_tags_mode" class="select_mode">
                            <option value="none" <?= $value["shortcode_tags_mode"] == 'none' ? "selected" : "" ?>>
                                nothing
                            </option>
                            <option value="all" <?= $value["shortcode_tags_mode"] == 'all' ? "selected" : "" ?>>
                                all tags
                            </option>
                            <option value="blacklist" <?= $value["shortcode_tags_mode"] == 'blacklist' ? "selected" : "" ?>>
                                only the following
                            </option>
                            <option value="whitelist" <?= $value["shortcode_tags_mode"] == 'all' ? "whitelist" : "" ?>>
                                only those other than
                            </option>
                        </select>
                    </label>
                    <div class="toggle_list shortcode_tags_mode <?=
					strpos( $value['shortcode_tags_mode'], "list" ) === false ? 'hidden' : ''
					?>">
                        <label>List of tags</label><br>
                        <textarea class="large-text code" name="shortcode_tags_list">
                            <?= isset($value['shortcode_tags_list']) ? $value['shortcode_tags_list'] : false ?>
                        </textarea>
                    </div>
                    <p class="description">
                        Some plugins declare <code>[shortcodes]</code> that you can use in the Post editor.
                        These shortcodes are commonly used to embed 3rd party content, like videos or maps. If you
                        think the shortcodes provided by the selected plugin are going to load a resource from another
                        website, you should probably block them preemptively.
                    </p>
                </fieldset>
            </td>
        </tr>
        <tr>
            <th scope="row">
                Vendor configuration
            </th>
            <td>
                <div class="form-field">
                    <p>
                        <label for="vendor_title">Title</label></p>
                    <p>
                        <input type="text" id="vendor_title" name="vendor_title" 
                        value="<?= isset($value['vendor_title']) ? $value['vendor_title'] : false ?>"
                        />
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_shortDescription">Short description</label>
                    </p>
                    <p>
                        <textarea id="vendor_shortDescription"
                                  name="vendor_shortDescription"
                        ><?= isset($value['vendor_shortDescription']) ? $value['vendor_shortDescription'] : false ?></textarea>
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_longDescription">Long description</label>
                    </p>
                    <p>
                        <textarea id="vendor_longDescription"
                                  name="vendor_longDescription"><?= isset($value['vendor_longDescription']) ? $value['vendor_longDescription'] : false ?></textarea>
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_policyUrl">Privacy Policy URL</label>
                    </p>
                    <p>
                        <input type="text" id="vendor_policyUrl" name="vendor_policyUrl"
                               value="<?= isset($value['vendor_policyUrl']) ? $value['vendor_policyUrl'] : false ?>"/>
                    </p>
                </div>
                <div class="form-field">
                    <p>
                        <label for="vendor_image">Image</label>
                    </p>
                    <p>
                        <input type="text" id="vendor_image" name="vendor_image" value="<?= isset($value['vendor_image']) ? $value['vendor_image'] : false ?>"/>
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
                           value="Save Changes">
                </p>
            </td>
        </tr>
    </table>
</form>
<script>
    jQuery(function ($) {
        $('.select_mode').on('change', function (e) {
            $('.toggle_list.' + e.target.name).toggleClass('hidden', !(e.target.value.indexOf('list') > -1));
        });

        $('#plugin_select').on('change', function (e) {
            $('#vendor_title').attr({placeholder: e.target.selectedOptions[0].dataset.title})
            $('#vendor_shortDescription').attr({placeholder: e.target.selectedOptions[0].dataset.description})
            $('#vendor_policyUrl').attr({placeholder: e.target.selectedOptions[0].dataset.uri})
        });

        function updateCookiesVersionDescription() {
            var selectedOption = $('#cookies_version')[0].selectedOptions[0];
            $('.description.cookies_version .all').toggleClass('hidden', selectedOption.value !== '');
            $('.description.cookies_version .selected').toggleClass('hidden', selectedOption.value === '');
            if(selectedOption.value.value !== ''){
                $('#axeptio_configuration_identifier').text(selectedOption.dataset.identifier)
                $('#axeptio_configuration_name').text(selectedOption.dataset.name)
                $('#axeptio_configuration_language').text(selectedOption.dataset.language)
            }
        }

        $('#cookies_version').on('change', updateCookiesVersionDescription);

        updateCookiesVersionDescription();
    })
</script>
