// noinspection ES6ConvertVarToLetConst
window.axeptioWordpressSteps = window.axeptioWordpressSteps || [];
window.axeptioWordpressVendors = window.axeptioWordpressVendors || [];
window.Axeptio_SDK = window.Axeptio_SDK || [];

window._axcb = window._axcb || [];

function generateKeyFromTrueValues(obj) {
	return Object.keys(obj)
		.filter(key => obj[key] === true)
		.join('_');
}

async function createHash(str) {
	const encoder = new TextEncoder();
	const data = encoder.encode(str);
	const hashBuffer = await crypto.subtle.digest('SHA-256', data);
	const hashArray = Array.from(new Uint8Array(hashBuffer));
	const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
	return hashHex;
}

function setCookie(name, value, days) {
	let expires = "";
	if (days) {
		let date = new Date();
		date.setTime(date.getTime() + (days*24*60*60*1000));
		expires = "; expires=" + date.toUTCString();
	}
	document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}

window._axcb.push( function( sdk ) {
	sdk.on( 'ready', function() {
		const selectedCookieConfigId = sdk.getCookiesConfig().identifier;

		sdk.config.cookies.map( function( cookieConfig ) {

			cookieConfig.steps[0].image = window.Axeptio_SDK.image ?? 'cookie-bienvenue';
			cookieConfig.steps[0].disablePaint = window.Axeptio_SDK.disablePaint ?? false;
			console.log(cookieConfig.steps[0]);

			if (window.Axeptio_SDK.enableGoogleConsentMode === '1') {
				cookieConfig.googleConsentMode = {
					display: true,
				}
				const hasGoogleConsentMode = cookieConfig?.steps?.some(step => step.layout === 'como_v2');

				if (cookieConfig?.googleConsentMode?.display && !hasGoogleConsentMode) {
					const como_v2_step = {
						name: 'google_consent_mode_v2',
						layout: 'como_v2',
						identifier: `google_consent_mode_v2_${Math.random().toString(36).substring(7)}`
					};
					cookieConfig?.steps.splice(1, 0, como_v2_step);
				}
			}

			// This avoids pushing vendors several times in the same WordPress steps
			// There's a drawback: we won't be able to change the cookies' version at runtime,
			// because the vendors are not up-to-date. One fix could be to switch to a dedicated function
			// that is allowed to "decorate" the actual cookie config before it's changed.
			// We could also add a new event in the SDK that would be triggered when the cookiesVersion is set
			if ( cookieConfig.identifier !== selectedCookieConfigId ) {
				return;
			}

			// Adding the additional steps coming from the WordPress configuration
			window.axeptioWordpressVendors.forEach( function( vendor ) {
				// the WP admin has decided to assign the vendor to a precise step
				// it can be one of the configuration or one defined in the WP plugin
				if ( vendor.step ) {
					let stepExists = false;

					// 1. Try to add it to the existing steps
					cookieConfig.steps.forEach( function( step ) {
						// We test the step layout in order to prevent adding a vendor
						// to a step layout that don't display them
						// Note: add it once because we may have duplicate names

						if ( step.name === vendor.step && ( step.layout === 'category' || step.layout === 'info' ) && ! stepExists ) {
							stepExists = true;
							step.vendors = step.vendors || [];
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					// 2. Try to add it the WordPress steps
					window.axeptioWordpressSteps.forEach( function( step ) {
						if ( step.name === vendor.step && ! stepExists ) {
							stepExists = true;
							step.vendors = step.vendors || [];
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					// 4. Add it to the first "category step"
					cookieConfig.steps.forEach( function( step ) {
						if ( step.category === 'category' && ! stepExists ) {
							stepExists = true; // Note: add it only once
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					// 5. Add it to the first "info step"
					cookieConfig.steps.forEach( function( step ) {
						if ( step.category === 'info' && ! stepExists ) {
							stepExists = true; // Note: add it only once
							step.vendors.push( vendor );
						}
					} );
					if ( stepExists ) {
						return;
					}

					console.warn( 'Could not add the Axeptio Plugin Vendor to the configuration' );
				}
			} );
			window.axeptioWordpressSteps.forEach( function( step ) {
				if ( step.vendors && step.vendors.length > 0 ) {
					// vendors have been assigned to the step, we need to add it to the cookie config
					// todo: take position and insert_position into account
					cookieConfig.steps.push( step );
				}
			} );

			// Sélectionnez tous les éléments avec l'attribut "data-axeptio-consent"
			const consentElements = document.querySelectorAll( '[data-axeptio-consent]' );

			// Parcourir tous les éléments sélectionnés
			if ( consentElements ) {
				document.querySelectorAll( '[data-axeptio-consent]' ).forEach( ( element ) => {
					element.addEventListener( 'click', () => {
						const consentValue = element.getAttribute( 'data-axeptio-consent' );
						console.log( consentValue );
						sdk.requestConsent( 'wp_' + consentValue );
					} );
				} );
			}
		} );
	} );

	sdk.on( 'cookies:complete', function( choices ) {
		const stringToHash = generateKeyFromTrueValues(choices);

		createHash(stringToHash).then(hash => {
			setCookie('axeptio_cache_identifier', hash, 7);
		});

		getAllComments( document.body ).forEach( function( comment ) {

			if ( comment.nodeValue.indexOf( 'axeptio_blocked' ) > -1 ) {
				const plugin = comment.nodeValue.match( /axeptio_blocked ([\w_-]+)/ )[ 1 ];
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
				comment.parentElement.replaceChild( elem.childNodes[ 0 ], comment );
			}
		} );
	} );

	function filterNone() {
		return NodeFilter.FILTER_ACCEPT;
	}

	function getAllComments( rootElem ) {
		const comments = [];
		// Fourth argument, which is actually obsolete according to the DOM4 standard, is required in IE 11
		// noinspection JSCheckFunctionSignatures
		const iterator = document.createNodeIterator( rootElem, NodeFilter.SHOW_COMMENT, filterNone, false );
		let curNode;
		while ( curNode = iterator.nextNode() ) {
			comments.push( curNode );
		}
		return comments;
	}
} );
