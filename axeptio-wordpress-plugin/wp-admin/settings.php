<?php

use Axeptio\Admin;

$admin = Admin::instance();
?>
<div class="wrap nosubsub">
    <h1 class="wp-heading-inline">Axeptio Plugin Settings</h1>
    <hr class="wp-header-end">

    <div id="ajax-response"></div>
    <form method="post">
        <input type="hidden" name="action" value="flush_cache" />
        <input type="submit" class="button" value="Reload Axeptio Configuration" />
    </form>
    <div class="form-wrap">
        <h2>Base settings</h2>
        <form id="axeptio_settings" method="post" class="validate">
            <input type="hidden" name="action" value="settings">
            <div class="form-field form-required term-name-wrap">
                <label for="client_id">Client ID</label>
                <input name="client_id"
                       id="client_id"
                       type="text"
                       value="<?php echo get_option( Admin::OPTION_CLIENT_ID ) ?>"
                       size="24"
                       maxlength="24"
                       aria-required="true"
                       aria-describedby="name-description">
                <p id="client_id-description">The Axeptio project ID</p>
            </div>
            <div class="form-field">
                <label for="cookies_version">Cookies Version</label>
                <select name="cookies_version">
                    <option value="">Dynamic: let Axeptio SDK decide based on your configuration</option>
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
                <label for="trigger_gtm_events">Trigger GTM Events</label>
                <input
                        type="checkbox"
                        id="trigger_gtm_events"
                        name="trigger_gtm_events"
			        <?php echo get_option(Admin::OPTION_TRIGGER_GTM_EVENT) ? "checked" : "" ?>
                />
            </div>
            <h3>User Cookies</h3>
            <p>This defines how behave the cookies written by the Axeptio SDK.</p>
            <div class="form-field">
                <label for="user_cookies_duration">Lifespan</label>
                <input
                    type="number"
                    min="0"
                    id="user_cookies_duration"
                    name="user_cookies_duration"
                    value="<?php echo get_option(Admin::OPTION_USER_COOKIES_DURATION) ?>"
                />
            </div>
            <div class="form-field">
                <label for="user_cookies_secure">Secure</label>
                <input
                    type="checkbox"
                    id="user_cookies_secure"
                    name="user_cookies_secure"
                    <?php echo get_option(Admin::OPTION_USER_COOKIES_SECURE) ? "checked" : "" ?>
                />
            </div>
            <div class="form-field">
                <label for="user_cookies_duration">Domain</label>
                <input
                        type="text"
                        id="user_cookies_domain"
                        name="user_cookies_domain"
                        value="<?php echo get_option(Admin::OPTION_USER_COOKIES_DOMAIN) ?>"
                />
            </div>
            <div class="form-field">
                <label for="authorized_vendors_cookie_name">Name of the "Authorized vendors" cookie</label>
                <input
                        type="text"
                        id="authorized_vendors_cookie_name"
                        name="authorized_vendors_cookie_name"
                        value="<?php echo get_option(Admin::OPTION_AUTHORIZED_VENDORS_COOKIE_NAME) ?>"
                />
            </div>
            <div class="form-field">
                <label for="json_cookie_name">Name of the cookie containing the preferences</label>
                <input
                        type="text"
                        id="json_cookie_name"
                        name="json_cookie_name"
                        value="<?php echo get_option(Admin::OPTION_JSON_COOKIE_NAME) ?>"
                />
            </div>
            <p class="submit">
                <input type="submit" name="submit" id="submit" class="button button-primary" value="Save">
                <span class="spinner"></span>
            </p>
        </form>
    </div>
</div>