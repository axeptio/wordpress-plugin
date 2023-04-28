<?php
/**
 * Cookie Analyzer
 *
 * Attempt to analyse if a plugin or theme uses cookies.
 *
 * @package Axeptio
 */

namespace Axeptio\Utils;

class Cookie_Analyzer {
	/**
	 * List of cookie related keywords.
	 *
	 * @var array[] $keywords
	 */
	private $keywords = array(
		5 => array( 'cookies', 'google' ),
		4 => array( 'tracking', 'pixel', 'analytics', 'remarketing', 'retargeting', 'targeting', 'facebook', 'linkedin', 'twitter', 'bing', 'yahoo' ),
		3 => array( 'marketing', 'advertisement', 'advertising', 'analysis', 'conversion', 'campaign', 'audience', 'ads', 'adwords', 'social', 'impression', 'views', 'engagement', 'reach', 'frequency', 'capping', 'track' ),
		2 => array( 'seo', 'form', 'sem', 'ppc', 'cpm', 'cpc', 'cpa', 'cps', 'affiliate', 'email', 'newsletter', 'subscription', 'crm', 'lead', 'landing', 'optimization', 'behavior', 'funnel', 'ab-testing', 'ab testing', 'personalization', 'segmentation', 'geolocation', 'heatmaps', 'scroll', 'bounce', 'rate', 'experience', 'gdpr', 'ccpa', 'lgpd' ),
		1 => array( 'mobile', 'desktop', 'tablet', 'device', 'referrer', 'traffic', 'source', 'medium', 'session', 'duration', 'goal', 'ecommerce', 'shop', 'cart', 'checkout', 'revenue', 'roi', 'return', 'investment', 'attribution', 'model', 'tag', 'manager', 'script', 'tracker', 'gtm', 'gtag', 'utm' ),
	);

	/**
	 * Cookie-free plugins.
	 *
	 * @var string[] $cookie_free_plugins
	 */
	private $cookie_free_plugins = array(
		'axeptio-sdk-integration',
		'axeptio-wordpress-plugin',
	);

	/**
	 * Plugins using cookies.
	 *
	 * @var string[] $cookie_plugin
	 */
	private $cookie_plugin = array(
		'google-analytics-for-wordpress', // MonsterInsights.
		'google-analytics-dashboard-for-wp', // ExactMetrics.
		'google-analytics', // Analytify.
		'ga-google-analytics', // GA Google Analytics.
		'jetpack', // Jetpack.
		'contact-form-7', // Contact Form 7.
		'yoast-seo', // Yoast SEO.
		'all-in-one-seo-pack', // All in One SEO Pack.
		'wordfence', // Wordfence Security.
		'w3-total-cache', // W3 Total Cache.
		'wp-super-cache', // WP Super Cache.
		'wp-rocket', // WP Rocket.
		'mailchimp-for-wp', // Mailchimp for WordPress.
		'hubspot', // HubSpot All-In-One Marketing.
		'shariff-wrapper', // Shariff Wrapper.
		'facebook-pixel', // Pixel Cat – Conversion Pixel Manager.
		'popup-maker', // Popup Maker.
		'cookie-notice', // Cookie Notice.
		'gdpr-cookie-compliance', // GDPR Cookie Compliance.
		'complianz-gdpr', // Complianz GDPR/CCPA.
		'google-tag-manager', // Google Tag Manager for WordPress.
		'woocommerce', // WooCommerce.
		'easy-digital-downloads', // Easy Digital Downloads.
		'wpforms-lite', // WPForms.
		'ninja-forms', // Ninja Forms.
		'gravityforms', // Gravity Forms.
		'caldera-forms', // Caldera Forms.
		'mailpoet', // MailPoet.
		'sumome', // SumoMe.
		'optinmonster', // OptinMonster.
		'convertplug', // ConvertPlug.
		'bloom', // Bloom.
		'thrive-leads', // Thrive Leads.
		'elementor', // Elementor.
		'beaver-builder-lite-version', // Beaver Builder.
		'wpbakery', // WPBakery Page Builder.
		'divi-builder', // Divi Builder.
		'bbpress', // bbPress.
		'buddypress', // BuddyPress.
		'memberpress', // MemberPress.
		'learndash', // LearnDash.
		'lifterlms', // LifterLMS.
		'social-warfare', // Social Warfare.
		'nextend-facebook-connect', // Nextend Social Login.
		'instagram-feed', // Smash Balloon Social Photo Feed.
		'akismet', // Akismet Anti-Spam.
		'disqus-comment-system', // Disqus Comment System.
		'clicky-analytics', // Clicky Analytics.
		'p3-profiler', // P3 (Plugin Performance Profiler).
		'updraftplus', // UpdraftPlus WordPress Backup Plugin.
		'onesignal-free-web-push-notifications', // OneSignal Web Push Notifications.
		'cookie-law-info', // GDPR Cookie Consent.
	);

	/**
	 * Analyze the plugins metas description
	 * to detect if the plugin may use cookies
	 *
	 * @param string $plugin_slug Slug of the plugin.
	 * @param string $title       Title of the plugin.
	 * @param string $description Description of the plugin.
	 * @return int
	 */
	public function analyze( string $plugin_slug, string $title, string $description ): int {
		if ( in_array( $plugin_slug, $this->cookie_plugin, true ) ) {
			return 100;
		}

		if ( in_array( $plugin_slug, $this->cookie_free_plugins, true ) ) {
			return 0;
		}

		$title_weight       = 3;
		$description_weight = 1;

		$title_score       = $this->calculate_score( $title, $title_weight );
		$description_score = $this->calculate_score( $description, $description_weight );

		$total_score = $title_score + $description_score;
		$max_score   = $title_weight + $description_weight;

		$percentage = ( $total_score / $max_score ) * 100;

		return (int) round( min( $percentage, 100 ) );
	}


	/**
	 * Calculate the score of the cookie probability.
	 *
	 * @param string $text           Text to be analyzed.
	 * @param int    $section_weight Weight of the section.
	 * @return int                   Calculated score.
	 */
	private function calculate_score( string $text, int $section_weight ): int {
		$score         = 0;
		$keyword_count = 0;
		$word_count    = str_word_count( $text );

		foreach ( $this->keywords as $weight => $keywords ) {
			foreach ( $keywords as $keyword ) {
				$pattern     = "/\b" . preg_quote( $keyword, '/' ) . "\b/i";
				$occurrences = preg_match_all( $pattern, $text, $matches );

				if ( $occurrences > 0 ) {
					$score         += $weight * $section_weight * $occurrences;
					$keyword_count += $occurrences;
				}
			}
		}

		// Adjust score based on keyword density.
		if ( $word_count > 0 ) {
			$keyword_density = $keyword_count / $word_count;
			$score          *= ( 1 + $keyword_density );
		}

		// Add a bonus for sections containing multiple unique keywords.
		$unique_keyword_count = count(
			array_filter(
				$this->keywords,
				function ( $keywords ) use ( $text ) {
					return count( array_intersect( $keywords, explode( ' ', strtolower( $text ) ) ) ) > 0;
				}
			)
		);

		if ( $unique_keyword_count > 1 ) {
			$bonus  = 1 + ( $unique_keyword_count - 1 ) * 0.1;
			$score *= $bonus;
		}

		return $score;
	}
}
