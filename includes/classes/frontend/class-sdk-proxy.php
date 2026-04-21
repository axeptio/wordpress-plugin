<?php
/**
 * Main Admin Page
 *
 * @package Axeptio
 */

namespace Axeptio\Plugin\Frontend;

use Axeptio\Plugin\Models\Settings;
use Axeptio\Plugin\Module;

class Sdk_Proxy extends Module {

	/**
	 * Constants for cache time and transient name
	 */
	const CACHE_TIME                 = DAY_IN_SECONDS;
	const TRANSIENT_KEY              = 'axeptio_sdk_content';
	private const ALLOWED_MIME_TYPES = array( 'application/javascript', 'text/javascript' );

	/**
	 * Check if the module can be registered.
	 *
	 * @return bool True if the module can be registered, false otherwise.
	 */
	public function can_register() {
		return true;
	}

	/**
	 * Register the module's actions and filters.
	 */
	public function register() {
		add_action( 'init', array( $this, 'add_rewrite_rules' ) );
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'proxy_cmp_js' ) );
		add_filter( 'redirect_canonical', array( $this, 'remove_trailing_slash' ), 20, 2 );
		add_action( 'update_option_axeptio_settings', array( $this, 'set_axeptio_settings' ), 20, 2 );
	}

	/**
	 * Set the axeptio settings when they are updated.
	 *
	 * @param mixed $old_value The old value of the settings.
	 * @param mixed $new_value The new value of the settings.
	 */
	public function set_axeptio_settings( $old_value, $new_value ) {
		if ( isset( $new_value['proxy_sdk'] ) && $new_value['proxy_sdk'] ) {
			$proxy_file = sanitize_title( wp_generate_password( 12, false ) );
			update_option( 'axeptio/sdk_proxy_key', $proxy_file );
		}
		update_option( 'axeptio/need_flush', '1' );

		// Supprimer le transient pour forcer un rechargement
		delete_transient( self::TRANSIENT_KEY );
	}

	/**
	 * Fetch the SDK content from remote URL
	 */
	private function fetch_sdk_content() {
		$external_url = 'https://static.axept.io/sdk.js';
		$response     = wp_remote_get( $external_url );

		if ( is_wp_error( $response ) ) {
			return false;
		}

		// Validate Content-Type against allowed MIME types.
		$content_type  = wp_remote_retrieve_header( $response, 'content-type' );
		$is_valid_mime = false;
		foreach ( self::ALLOWED_MIME_TYPES as $allowed_type ) {
			if ( strpos( $content_type, $allowed_type ) !== false ) {
				$is_valid_mime = true;
				break;
			}
		}
		if ( ! $is_valid_mime ) {
			return false;
		}

		$content = wp_remote_retrieve_body( $response );

		if ( empty( $content ) ) {
			return false;
		}

		// Nettoyer et normaliser le contenu JavaScript
		$content = str_replace( array( "\r\n", "\r" ), "\n", $content );

		// Stocker le contenu dans un transient
		set_transient( self::TRANSIENT_KEY, $content, self::CACHE_TIME );

		return $content;
	}

	/**
	 * Serve the SDK proxy content
	 */
	public function proxy_cmp_js() {
		if ( ! get_query_var( 'proxy_axeptio_sdk' ) ) {
			return;
		}

		// Récupérer le contenu du cache
		$sdk_content = get_transient( self::TRANSIENT_KEY );

		// Si le cache est vide ou expiré, récupérer le contenu distant
		if ( false === $sdk_content ) {
			$sdk_content = $this->fetch_sdk_content();

			if ( false === $sdk_content ) {
				wp_die( 'Error fetching SDK content' );
			}
		}

		// Headers de sécurité
		nocache_headers();
		header( 'Content-Type: application/javascript; charset=utf-8' );
		header( 'X-Content-Type-Options: nosniff' );

		// Autres headers de sécurité
		header( 'X-Frame-Options: DENY' );
		header( 'X-XSS-Protection: 1; mode=block' );

		// S'assurer que le contenu est bien encodé avant de l'envoyer
		$sdk_content = wp_check_invalid_utf8( $sdk_content );
		header( 'Content-Length: ' . strlen( $sdk_content ) );

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SDK JavaScript content validated by wp_check_invalid_utf8.
		echo $sdk_content;
		exit;
	}

	/**
	 * Remove trailing slash from the redirect URL if the 'proxy_axeptio_sdk' query var is present.
	 *
	 * @param string $redirect_url The URL to redirect to.
	 * @param string $requested_url The URL requested by the user.
	 * @return string The modified redirect URL.
	 */
	public function remove_trailing_slash( $redirect_url, $requested_url ) {
		if ( ! get_query_var( 'proxy_axeptio_sdk' ) ) {
			return $redirect_url;
		}
		return rtrim( $redirect_url, '/' );
	}

	/**
	 * Add rewrite rules for the SDK proxy.
	 */
	public function add_rewrite_rules() {
		if ( Settings::get_option( 'proxy_sdk', false ) ) {
			$sdk = $this->get_sdk_proxy_key();
			add_rewrite_rule( '^' . $sdk . '\.js$', 'index.php?proxy_axeptio_sdk=1', 'top' );
		}

		if ( get_option( 'axeptio/need_flush', '0' ) ) {
			flush_rewrite_rules();
			update_option( 'axeptio/need_flush', '0' );
		}
	}

	/**
	 * Get the SDK proxy key from the options.
	 *
	 * @return string The SDK proxy key.
	 */
	protected function get_sdk_proxy_key() {
		return get_option( 'axeptio/sdk_proxy_key' );
	}

	/**
	 * Add the 'proxy_axeptio_sdk' query var to the list of query vars.
	 *
	 * @param array $vars The list of query vars.
	 * @return array The updated list of query vars.
	 */
	public function add_query_vars( $vars ) {
		$vars[] = 'proxy_axeptio_sdk';
		return $vars;
	}
}
