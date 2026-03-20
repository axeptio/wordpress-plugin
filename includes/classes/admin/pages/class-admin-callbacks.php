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

	public function widget_set_options() {
		\Axeptio\Plugin\get_template_part( 'admin/main/fields/widget-options', array( 'widgets' => Settings::get_option( 'xpwp_version_options', '', false ) ) );
	}

	/**
	 * Display widget fields for a given language
	 *
	 * @param string $language_code Language code
	 */
	public static function render_widget_fields( string $language_code ): void {
		self::widget_title( array( 'language' => $language_code ) );
		self::widget_subtitle( array( 'language' => $language_code ) );
		self::widget_description( array( 'language' => $language_code ) );
	}

	/**
	 * Extract language code from arguments
	 *
	 * @param array $args Arguments
	 * @return string Language code or empty string
	 */
	private static function get_language_from_args( array $args ): string {
		return $args['language'] ?? '';
	}

	/**
	 * Format label with language code if needed
	 *
	 * @param string $label Base label
	 * @param string $language Language code
	 * @return string Formatted label
	 */
	private static function format_label_with_language( string $label, string $language ): string {
		return $language ? sprintf( '%s (%s)', $label, $language ) : $label;
	}

	/**
	 * Generate field attributes with language suffix if needed
	 *
	 * @param string $base_name Base name of the field
	 * @param string $language Language code
	 * @return array Field attributes with name and id
	 */
	private static function get_field_attributes( string $base_name, string $language ): array {
		$suffix = $language ? "_{$language}" : '';
		return array(
			'name' => $base_name . $suffix,
			'id'   => 'xpwp_' . $base_name . $suffix,
		);
	}

	/**
	 * Get option value with language support and fallback
	 *
	 * @param string $base_name Base option name
	 * @param string $language Language code
	 * @return string Option value
	 */
	private static function get_widget_value( string $base_name, string $language ): string {
		if ( $language ) {
			$value = Settings::get_option( $base_name . '_' . $language, null );
			if ( null !== $value ) {
				return $value;
			}
		}

		return Settings::get_option( $base_name, '' );
	}

	/**
	 * Render a widget field with proper template
	 *
	 * @param array $args Field configuration
	 * @return void
	 */
	private static function render_widget_field( array $args ): void {
		$language   = $args['language'] ?? '';
		$field_name = $args['field_name'];
		$label      = $args['label'];
		$template   = $args['template'] ?? 'admin/common/fields/text';

		$field_attrs = self::get_field_attributes( $field_name, $language );
		$value       = self::get_widget_value( $field_name, $language );

		\Axeptio\Plugin\get_template_part(
			$template,
			array_merge(
				array(
					'label'    => self::format_label_with_language( $label, $language ),
					'group'    => 'axeptio_settings',
					'value'    => $value,
					'language' => $language,
				),
				$field_attrs
			)
		);
	}

	/**
	 * Title of the widget.
	 *
	 * @param array $args Arguments passed to the function.
	 * @return void
	 */
	public static function widget_title( array $args = array() ): void {
		self::render_widget_field(
			array(
				'field_name' => 'widget_title',
				'label'      => __( 'Widget title', 'axeptio-wordpress-plugin' ),
				'language'   => self::get_language_from_args( $args ),
			)
			);
	}

	/**
	 * Sub-title of the widget.
	 *
	 * @param array $args Arguments passed to the function.
	 * @return void
	 */
	public static function widget_subtitle( array $args = array() ): void {
		self::render_widget_field(
			array(
				'field_name' => 'widget_subtitle',
				'label'      => __( 'Widget sub-title', 'axeptio-wordpress-plugin' ),
				'language'   => self::get_language_from_args( $args ),
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
				'label'       => __( 'Cookie domain', 'axeptio-wordpress-plugin' ) . ' (userCookieDomain)',
				'group'       => 'axeptio_settings',
				'name'        => 'cookie_domain',
				'id'          => 'xpwp_cookie_domain',
				'value'       => Settings::get_option( 'cookie_domain', '' ),
				'instruction' => __( 'If specified, domain name on which the cookie containing user choices will be available. This allows to request one consent for various subdomains', 'axeptio-wordpress-plugin' ),
				'help_url'    => strpos( get_user_locale(), 'fr' ) === 0 ? 'https://support.axeptio.eu/fr/articles/274095-comment-parametrer-le-widget-pour-un-sous-domaine' : 'https://support.axeptio.eu/en/articles/274095-how-to-set-the-widget-for-a-sub-domain',
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
				'label'       => __( 'URL for server-side usage', 'axeptio-wordpress-plugin' ) . ' (postConsentUrl)',
				'group'       => 'axeptio_settings',
				'type'        => 'url',
				'name'        => 'api_url',
				'id'          => 'xpwp_api_url',
				'value'       => Settings::get_option( 'api_url', '' ),
				'instruction' => __( 'URL to which the widget will send POST requests after user consent.', 'axeptio-wordpress-plugin' ),
				'help_url'    => strpos( get_user_locale(), 'fr' ) === 0 ? 'https://support.axeptio.eu/fr/articles/274016-mise-en-place-du-server-side-tracking' : 'https://support.axeptio.eu/hc/en-gb/articles/28447238691345-Passing-Consent-in-Your-GTM-Server-side-Container',
				'placeholder' => 'https://yourdomain.clouds',
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
	 * @param array $args Arguments passed to the function.
	 * @return void
	 */
	public static function widget_description( array $args = array() ): void {
		self::render_widget_field(
			array(
				'field_name' => 'widget_description',
				'label'      => __( 'Widget description', 'axeptio-wordpress-plugin' ),
				'language'   => self::get_language_from_args( $args ),
				'template'   => 'admin/common/fields/textarea',
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
				'help_url'    => strpos( get_user_locale(), 'fr' ) === 0 ? 'https://support.axeptio.eu/fr/articles/274038-gestion-des-evenements-personnalises-google-tag-manager' : 'https://support.axeptio.eu/hc/en-gb/articles/27662718518929-Management-of-Custom-Events-in-Google-Tag-Manager',
			)
		);
	}
}
