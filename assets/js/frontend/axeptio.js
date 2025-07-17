// noinspection ES6ConvertVarToLetConst
window.axeptioWordpressSteps = window.axeptioWordpressSteps || [];
window.axeptioWordpressVendors = window.axeptioWordpressVendors || [];
window.Axeptio_SDK = window.Axeptio_SDK || [];

window._axcb = window._axcb || [];

window.wp_consent_type = 'optin';
if (typeof wp_set_consent === 'function') {
	Object.entries(window.axeptioSettings.googleConsentMode.default).forEach(([key, value]) => {
		wp_set_consent(key, 'deny');
	});
}
let consentTypeEvent = new CustomEvent('wp_consent_type_defined');
document.dispatchEvent(consentTypeEvent);

function generateKeyFromTrueValues( obj ) {
	return Object.keys( obj )
		.filter( ( key ) => obj[ key ] === true )
		.join( '_' );
}

async function createHash( str ) {
	// eslint-disable-next-line no-undef
	const encoder = new TextEncoder();
	const data = encoder.encode( str );
	// eslint-disable-next-line no-undef
	const hashBuffer = await crypto.subtle.digest( 'SHA-256', data );
	const hashArray = Array.from( new Uint8Array( hashBuffer ) );
	return hashArray.map( ( b ) => b.toString( 16 ).padStart( 2, '0' ) ).join( '' );
}

function setCookie( name, value, days ) {
	let expires = '';
	if ( days ) {
		const date = new Date();
		date.setTime( date.getTime() + ( days * 24 * 60 * 60 * 1000 ) );
		expires = '; expires=' + date.toUTCString();
	}
	document.cookie = name + '=' + ( value || '' ) + expires + '; path=/';
}

window._axcb.push( function( sdk ) {
	sdk.on( 'ready', function() {
		const selectedCookieConfigId = sdk.getCookiesConfig().identifier;
		const selectedConfig = sdk.config.cookies.find(config => config.identifier === selectedCookieConfigId);
		const currentLanguage = selectedConfig?.language || 'en';

		sdk.config.cookies.forEach( function( cookieConfig ) {
			if ( window.Axeptio_SDK.enableGoogleConsentMode === '1' ) {
				cookieConfig.googleConsentMode = {
					display: true,
				};
				const hasGoogleConsentMode = cookieConfig?.steps?.some( ( step ) => step.layout === 'como_v2' );

				if ( cookieConfig?.googleConsentMode?.display && ! hasGoogleConsentMode ) {
					// eslint-disable-next-line camelcase
					const como_v2_step = {
						name: 'google_consent_mode_v2',
						layout: 'como_v2',
						identifier: `google_consent_mode_v2_${ Math.random().toString( 36 ).substring( 7 ) }`,
					};
					cookieConfig?.steps.splice( 1, 0, como_v2_step );
				}
			}

			if ( cookieConfig.identifier !== selectedCookieConfigId ) {
				return;
			}

			window.axeptioWordpressVendors.forEach( function( vendor ) {
				if ( vendor.step ) {
					let stepExists = false;

					cookieConfig.steps.forEach( function( step ) {
						if ( step.name === vendor.step && ( step.layout === 'category' || step.layout === 'info' ) && ! stepExists ) {
							stepExists = true;

							step.vendors = step.vendors || [];
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					window.axeptioWordpressSteps.forEach( function( step ) {
						if ( step.name === vendor.step && ! stepExists ) {
							stepExists = true;

							const title = window.Axeptio_SDK[`widget_title_${currentLanguage}`] || window.Axeptio_SDK.widget_title;
							const subTitle = window.Axeptio_SDK[`widget_subtitle_${currentLanguage}`] || window.Axeptio_SDK.widget_subtitle;
							const message = window.Axeptio_SDK[`widget_description_${currentLanguage}`] || window.Axeptio_SDK.widget_description;

							if (title) step.title = title;
							if (subTitle) step.subTitle = subTitle;
							if (message) step.message = message;

							step.image = window.Axeptio_SDK.image ?? 'cookie-bienvenue';
							step.disablePaint = window.Axeptio_SDK.disablePaint ?? false;
							step.vendors = step.vendors || [];
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					cookieConfig.steps.forEach( function( step ) {
						if ( step.category === 'category' && ! stepExists ) {
							stepExists = true;
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					cookieConfig.steps.forEach( function( step ) {
						if ( step.category === 'info' && ! stepExists ) {
							stepExists = true;
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					// eslint-disable-next-line no-console
					console.warn( 'Could not add the Axeptio Plugin Vendor to the configuration' );
				}
			} );
			window.axeptioWordpressSteps.forEach( function( step ) {
				if ( step.vendors && step.vendors.length > 0 ) {
					cookieConfig.steps.push( step );
				}
			} );

			const consentElements = document.querySelectorAll( '[data-axeptio-consent]' );

			if ( consentElements ) {
				document.querySelectorAll( '[data-axeptio-consent]' ).forEach( ( element ) => {
					element.addEventListener( 'click', () => {
						const consentValue = element.getAttribute( 'data-axeptio-consent' );
						sdk.requestConsent( 'wp_' + consentValue );
					} );
				} );
			}
		} );
	} );

	sdk.on( 'cookies:complete', function( choices ) {
		const stringToHash = generateKeyFromTrueValues( choices );

		createHash( stringToHash ).then( ( hash ) => {
			setCookie( 'axeptio_cache_identifier', hash, 7 );
		} );

		// WP_Consent_Api
		if (typeof wp_set_consent === 'function' && window.Axeptio_SDK.enableGoogleConsentMode === '1'
			&& choices.$$googleConsentMode) {

			const consentMapping = {
				'ad_storage': 'marketing',
				'ad_user_data': 'marketing',
				'ad_personalization': 'marketing',
				'analytics_storage': 'statistics',
				'functionality_storage': 'functional',
				'personalization_storage': 'preferences',
				'security_storage': 'functional'
			};

			const gcmConsents = choices.$$googleConsentMode;
			const wpConsents = {};

			Object.keys(gcmConsents).forEach(gcmKey => {
				if (consentMapping[gcmKey]) {
					const wpCategory = consentMapping[gcmKey];
					const gcmValue = gcmConsents[gcmKey];
					const wpValue = gcmValue === 'granted' ? 'allow' : 'deny';

					// Only update if the value is more permissive than the one already set
					// For marketing, just one property being allowed is enough
					if (!wpConsents[wpCategory] || (wpValue === 'allow' && wpConsents[wpCategory] !== 'allow')) {
						wpConsents[wpCategory] = wpValue;
					}
				}
			});

			Object.keys(wpConsents).forEach(wpCategory => {
				wp_set_consent(wpCategory, wpConsents[wpCategory]);
			});

			let consentChangeEvent = new CustomEvent('wp_consent_change', {
				detail: wpConsents
			});
			document.dispatchEvent(consentChangeEvent);
		}

		getAllComments( document.body ).forEach( function( comment ) {
			if ( comment.nodeValue.indexOf( 'axeptio_blocked' ) > -1 ) {
				const plugin = comment.nodeValue.match( /axeptio_blocked ([\w_-]+)/ )[ 1 ];
				const attributes = comment.nodeValue.match( /data-axeptio-attributes="([^"]+)"/ );

				if ( ! choices[ 'wp_' + plugin ] ) {
					return;
				}

				const placeholder = comment.previousElementSibling;
				if ( placeholder ) {
					placeholder.remove();
				}

				const value = comment.nodeValue.split( '\n' ).slice( 1 ).join( '\n' );
				const elem = document.createElement( 'div' );
				elem.innerHTML = value;

				if (attributes && attributes[1]) {
					const attributesList = attributes[1].split(',');
					if (attributesList.includes('forceReload')) {
						window.location.reload();
						return;
					}
				}

				comment.parentElement.replaceChild( elem.childNodes[ 0 ], comment );
			}
		} );
	} );

	function filterNone() {
		// eslint-disable-next-line no-undef
		return NodeFilter.FILTER_ACCEPT;
	}

	function getAllComments( rootElem ) {
		const comments = [];
		// Fourth argument, which is actually obsolete according to the DOM4 standard, is required in IE 11
		// noinspection JSCheckFunctionSignatures
		// eslint-disable-next-line no-undef
		const iterator = document.createNodeIterator( rootElem, NodeFilter.SHOW_COMMENT, filterNone, false );
		let curNode;
		// eslint-disable-next-line no-cond-assign
		while ( curNode = iterator.nextNode() ) {
			comments.push( curNode );
		}
		return comments;
	}
} );
