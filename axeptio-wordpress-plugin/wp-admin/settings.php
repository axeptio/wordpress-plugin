<?php

use Axeptio\Admin;

$admin = Admin::instance();
?>
<div class="wrap nosubsub">
    <h1 class="wp-heading-inline"><?= __('Axeptio Plugin Settings', 'axeptio-wordpress-plugin')?></h1>
    <hr class="wp-header-end">

    <div id="ajax-response"></div>
    <form method="post">
        <input type="hidden" name="action" value="flush_cache" />
        <input type="submit" class="button" value="<?= __('Reload Axeptio Configuration', 'axeptio-wordpress-plugin')?>" />
    </form>
    <div class="form-wrap">
        <h2><?= __('Base settings', 'axeptio-wordpress-plugin')?></h2>
        <form id="axeptio_settings" method="post" class="validate">
            <input type="hidden" name="action" value="settings">
            <div class="form-field form-required term-name-wrap">
                <label for="client_id"><?= __('Client ID', 'axeptio-wordpress-plugin')?></label>
                <input name="client_id"
                       id="client_id"
                       type="text"
                       value="<?php echo get_option( Admin::OPTION_CLIENT_ID ) ?>"
                       size="24"
                       maxlength="24"
                       aria-required="true"
                       aria-describedby="name-description">
                <p id="client_id-description"><?= __('The Axeptio project ID', 'axeptio-wordpress-plugin')?></p>
            </div>
            <div class="form-field">
                <label for="cookies_version"><?= __('Cookies Version', 'axeptio-wordpress-plugin')?></label>
                <select name="cookies_version" id="cookies_version">
                    <option value=""><?= __('Dynamic: let Axeptio SDK decide based on your configuration', 'axeptio-wordpress-plugin')?></option>
					<?php
					$savedCookiesVersion = get_option( Admin::OPTION_COOKIES_VERSION );
					foreach ( $admin->axeptioConfiguration->cookies as $cookieConfiguration ) {
						$selected = $cookieConfiguration->identifier == $savedCookiesVersion ? 'selected="selected"' : '';
						echo "
                            <option value='$cookieConfiguration->identifier' $selected>
                                $cookieConfiguration->name
                            </option>";
					}
					?>
                </select>
            </div>
            <div class="form-field">
                <label for="trigger_gtm_events"><?= __('Trigger GTM Events', 'axeptio-wordpress-plugin')?></label>
                <input
                        type="checkbox"
                        id="trigger_gtm_events"
                        name="trigger_gtm_events"
			        <?php echo get_option(Admin::OPTION_TRIGGER_GTM_EVENT) ? "checked" : "" ?>
                />
            </div>
            <h3><?= __('User Cookies', 'axeptio-wordpress-plugin')?></h3>
            <p><?= __('This defines how behave the cookies written by the Axeptio SDK.', 'axeptio-wordpress-plugin')?></p>
            <div class="form-field">
                <label for="user_cookies_duration"><?= __('Lifespan', 'axeptio-wordpress-plugin')?></label>
                <input
                    type="number"
                    min="0"
                    id="user_cookies_duration"
                    name="user_cookies_duration"
                    value="<?php echo get_option(Admin::OPTION_USER_COOKIES_DURATION) ?>"
                />
            </div>
            <div class="form-field">
                <label for="user_cookies_secure"><?= __('Secure', 'axeptio-wordpress-plugin')?></label>
                <input
                    type="checkbox"
                    id="user_cookies_secure"
                    name="user_cookies_secure"
                    <?php echo get_option(Admin::OPTION_USER_COOKIES_SECURE) ? "checked" : "" ?>
                />
            </div>
            <div class="form-field">
                <label for="user_cookies_domain"><?= __('Domain', 'axeptio-wordpress-plugin')?></label>
                <input
                        type="text"
                        id="user_cookies_domain"
                        name="user_cookies_domain"
                        value="<?php echo get_option(Admin::OPTION_USER_COOKIES_DOMAIN) ?>"
                />
            </div>
            <div class="form-field">
                <label for="authorized_vendors_cookie_name"><?= __('Name of the "Authorized vendors" cookie', 'axeptio-wordpress-plugin')?></label>
                <input
                        type="text"
                        id="authorized_vendors_cookie_name"
                        name="authorized_vendors_cookie_name"
                        value="<?php echo get_option(Admin::OPTION_AUTHORIZED_VENDORS_COOKIE_NAME) ?>"
                />
            </div>
            <div class="form-field">
                <label for="json_cookie_name"><?= __('Name of the cookie containing the preferences', 'axeptio-wordpress-plugin')?></label>
                <input
                        type="text"
                        id="json_cookie_name"
                        name="json_cookie_name"
                        value="<?php echo get_option(Admin::OPTION_JSON_COOKIE_NAME) ?>"
                />
            </div>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="<?= __('Save', 'axeptio-wordpress-plugin')?>">
                <span class="spinner"></span>
            </p>
        </form>
    </div>
</div>


<script>
    jQuery(function ($) {
        //loadVersions('#client_id').val());

        $("#client_id").on("input", function() {
            $('#cookies_version').empty();
            loadVersions($('#client_id').val());
        });

        function loadVersions(clientId) {
            var select = $('#cookies_version');
            select.append($("<option></option>").attr("value", "").text("<?= __('Dynamic: let Axeptio SDK decide based on your configuration', 'axeptio-wordpress-plugin')?>"));
            jQuery.ajax({
                type: 'get',
                dataType: 'json',
                url: 'https://client.axept.io/' + clientId + '.json',
                success: function (response) {
                    for (let i = 0; i < response.cookies.length; i++) {
                        let title = response.cookies[i].title;
                        select.append($("<option></option>").attr("value", title).text(title));
                    }
                }
            });
        }
    })
</script>
