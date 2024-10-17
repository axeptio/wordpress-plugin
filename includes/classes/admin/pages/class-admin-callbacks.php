<?php
/**
 * Admin Callbacks
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Admin\Pages;

use Axeptio\Plugin\Models\Axeptio_Steps;
use Axeptio\Plugin\Models\Hook_Modes;
use Axeptio\Plugin\Models\Plugins;
use Axeptio\Plugin\Models\Project_Versions;
use Axeptio\Plugin\Models\Settings;
use Axeptio\Plugin\Models\Shortcode_Tags_Modes;

class Admin_Callbacks {
	/**
	 * Admin dashboard callback.
	 *
	 * @return resource
	 */
	public function admin_dashboard() {
		return require_once XPWP_PATH . 'templates' . DS . 'admin' . DS . 'settings-main.php';
	}

	/**
	 * Plugin manager callback.
	 *
	 * @return resource
	 */
	public function plugin_manager() {
		$settings = array(
			'nonce'            => wp_create_nonce( 'wp_rest' ),
			'active_plugins'   => Plugins::get_active_plugins(),
			'project_versions' => Project_Versions::all(),
		);
		return require_once XPWP_PATH . 'templates' . DS . 'admin' . DS . 'plugin-manager.php';
	}

	/**
	 * Options group
	 *
	 * @param mixed $input The input value.
	 * @return mixed
	 */
	public function options_group( $input ) {
		return $input;
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function sdk_active_set() {
		\Axeptio\Plugin\get_template_part( 'admin/main/fields/sdk-active' );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function google_consent_mode_set() {
		\Axeptio\Plugin\get_template_part( 'admin/main/fields/google-consent-mode' );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function send_datas_set() {
		\Axeptio\Plugin\get_template_part( 'admin/main/fields/send-datas' );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function client_id_set() {
		\Axeptio\Plugin\get_template_part( 'admin/main/fields/client-id' );
	}

	/**
	 * JSON Options list (hidden field).
	 *
	 * @return void
	 */
	public function version_set_options() {
		\Axeptio\Plugin\get_template_part( 'admin/main/fields/version-options', array( 'versions' => Settings::get_option( 'xpwp_version_options', '', false ) ) );
	}

	/**
	 * Options page
	 *
	 * @return void
	 */
	public function version_set() {
		\Axeptio\Plugin\get_template_part(
			'admin/main/fields/version',
			array(
				'version'     => Project_Versions::all(),
				'option_keys' => array_values( Project_Versions::get_localized_versions() ),
			)
		);
	}

	/**
	 * Account panel
	 *
	 * @return void
	 */
	public function display_onboarding_account_panel() {
		\Axeptio\Plugin\get_template_part( 'admin/onboarding/account' );
	}

	/**
	 * Title of the widget.
	 *
	 * @return void
	 */
	public function widget_title() {
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/text',
			array(
				'label' => __( 'Widget title', 'axeptio-wordpress-plugin' ),
				'group' => 'axeptio_settings',
				'name'  => 'widget_title',
				'id'    => 'xpwp_widget_title',
				'value' => Axeptio_Steps::get_title(),
			)
			);
	}

	/**
	 * Sub-title of the widget.
	 *
	 * @return void
	 */
	public function widget_subtitle() {
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/text',
			array(
				'label' => __( 'Widget sub-title', 'axeptio-wordpress-plugin' ),
				'group' => 'axeptio_settings',
				'name'  => 'widget_subtitle',
				'id'    => 'xpwp_widget_subtitle',
				'value' => Axeptio_Steps::get_sub_title(),
			)
			);
	}

	/**
	 * Display widget image upload field.
	 *
	 * @return void
	 */
	public function widget_image() {
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/image-upload',
			array(
				'label' => __( 'Widget Image', 'axeptio-wordpress-plugin' ),
				'group' => 'axeptio_settings',
				'name'  => 'widget_image',
				'id'    => 'xpwp_widget_image',
				'value' => \Axeptio\Plugin\Models\Settings::get_option( 'widget_image', '' ),
			)
		);
	}

	/**
	 * Display widget background image settings.
	 *
	 * @return void
	 */
	public function widget_background_image() {
		\Axeptio\Plugin\get_template_part(
			'admin/main/fields/background-image',
			array(
				'label'       => __( 'Disable the background Image', 'axeptio-wordpress-plugin' ),
				'description' => __( 'By checking this box, you will deactivate the painted background.', 'axeptio-wordpress-plugin' ),
				'group'       => 'axeptio_settings',
				'name'        => 'widget_disable_paint',
				'id'          => 'xpwp_widget_disable_paint',
				'value'       => \Axeptio\Plugin\Models\Settings::get_option( 'widget_disable_paint', '' ),
			)
		);
	}


	/**
	 * Cookie domain.
	 *
	 * @return void
	 */
	public function cookie_domain() {
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/text',
			array(
				'label'       => __( 'Cookie domain', 'axeptio-wordpress-plugin' ),
				'group'       => 'axeptio_settings',
				'name'        => 'cookie_domain',
				'id'          => 'xpwp_cookie_domain',
				'value'       => Settings::get_option( 'cookie_domain', '' ),
				'instruction' => __( 'If specified, domain name on which the cookie containing user choices will be available. This allows to request one consent for various subdomains', 'axeptio-wordpress-plugin' ),
			)
		);
	}


	/**
	 * Cookie domain.
	 *
	 * @return void
	 */
	public function api_url() {
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/text',
			array(
				'label'       => __( 'Post consent URL', 'axeptio-wordpress-plugin' ),
				'group'       => 'axeptio_settings',
				'name'        => 'api_url',
				'id'          => 'xpwp_api_url',
				'value'       => Settings::get_option( 'api_url', '' ),
				'instruction' => __( 'URL to which the widget will send POST requests after user consent.', 'axeptio-wordpress-plugin' ),
			)
		);
	}

	/**
	 * Cookie domain.
	 *
	 * @return void
	 */
	public function proxy_sdk() {
		\Axeptio\Plugin\get_template_part(
			'admin/main/fields/proxy-sdk',
			array(
				'label'       => __( 'Enable sdk proxy', 'axeptio-wordpress-plugin' ),
				'group'       => 'axeptio_settings',
				'name'        => 'sdk_proxy',
				'id'          => 'xpwp_sdk_proxy',
				'value'       => Settings::get_option( 'sdk_proxy', '0' ),
				'instruction' => __( 'Load the Axeptio SDK from your website domain.', 'axeptio-wordpress-plugin' ),
			)
		);
	}

	/**
	 * Description of the widget.
	 *
	 * @return void
	 */
	public function widget_description() {
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/textarea',
			array(
				'label' => __( 'Widget description', 'axeptio-wordpress-plugin' ),
				'group' => 'axeptio_settings',
				'name'  => 'widget_description',
				'id'    => 'xpwp_widget_description',
				'value' => Axeptio_Steps::get_description(),
			)
			);
	}

	/**
	 * Display notice for reviews.
	 *
	 * @return void
	 */
	public function add_admin_notice_for_review() {
		if ( ! \Axeptio\Plugin\Models\Notice::is_displayable() ) {
			return;
		}
		\Axeptio\Plugin\get_template_part(
			'admin/sections/notice'
		);
	}

	/**
	 * Callback function to display the API URL input field.
	 *
	 * @return void
	 */
	public function api_url_callback() {
		$api_url = Settings::get_option( 'api_url' );
		?>
		<input type="text" name="axeptio_settings[api_url]" value="<?php echo esc_attr( $api_url ); ?>" class="regular-text">
		<p class="description">
			<?php esc_html_e( 'URL to which the widget will send POST requests after user consent.', 'axeptio-wordpress-plugin' ); ?>
		</p>
		<?php
	}

	/**
	 * Google Tag Manager Event settings.
	 *
	 * @return void
	 */
	public function gtm_events_set() {
		\Axeptio\Plugin\get_template_part(
			'admin/common/fields/gtm-events',
			array(
				'label'       => __( 'Google Tag Manager Events', 'axeptio-wordpress-plugin' ),
				'description' => __( 'Configure how events are sent to Google Tag Manager', 'axeptio-wordpress-plugin' ),
				'group'       => 'axeptio_settings',
				'name'        => 'gtm_events',
				'id'          => 'xpwp_gtm_events',
				'value'       => \Axeptio\Plugin\Models\Settings::get_option( 'gtm_events', 'true' ),
			)
		);
	}
}
