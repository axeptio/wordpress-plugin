import Alpine from 'alpinejs';

const instance = function( args ) {
	return {
		axeptioSettings: {},
		accountID: args.accountID,
		showID: false,
		errorMessage: '',
		isHistorizedVersion: false,
		validAccountID: false,
		activeSDK: args.activeSDK,
		activeGoogleConsentMode: args.activeGoogleConsentMode,
		googleConsentModeParams: args.googleConsentModeParams,
		historizedVersions: args.historizedVersions,
		sendDatas: args.sendDatas,
		proxySdk: args.proxySdk,
		currentTab: Alpine.$persist( 'main-settings' ),
		selectedOption: args.selectedOption,
		options: ( () => {
			try {
				return JSON.parse( args.optionsJson );
			} catch {
				return [];
			}
		} )(),
		optionsJson: args.optionsJson,
		init() {
			this.axeptioSettings = window.Axeptio;

			// Récupération de la valeur du champ caché et affectation à accountID
			if ( this.accountID === '' ) {
				return;
			}

			this.showID = true;
			this.validAccountID = true;

			if ( this.options.length === 0 ) {
				this.validateAccountID();
			}
		},
		restoreHistorizedVersion() {
			this.selectedOption = this.historizedVersions[ this.accountID ];
			this.isHistorizedVersion = false;
		},
		async validateAccountID() {
			const { errors } = this.axeptioSettings;
			this.errorMessage = '';

			if ( this.accountID.trim() === '' ) {
				this.validAccountID = false;
				this.errorMessage = errors.empty_account_id;
				return;
			}

			try {
				const url = `https://client.axept.io/${ this.accountID }.json?nocache=${ Date.now() }`;
				const response = await fetch( url );

				if ( ! response.ok ) {
					throw new Error( `Unexpected response status: ${ response.status }` );
				}

				const data = await response.json();

				// Unpublished or non-existent project: API returns { isEmpty: true }.
				if ( data.isEmpty === true ) {
					this.validAccountID = false;
					this.errorMessage = errors.non_existing_account_id;
					return;
				}

				// Published project with no banner configured: cookies array is empty.
				if ( ! Array.isArray( data.cookies ) || data.cookies.length === 0 ) {
					this.validAccountID = false;
					this.errorMessage = errors.empty_cookies;
					return;
				}

				this.options = data.cookies.map( ( cookie ) => ( {
					value: cookie.name,
					text: cookie.title,
				} ) );
				this.optionsJson = JSON.stringify( this.options );
				this.showID = true;
				this.validAccountID = true;
				this.isHistorizedVersion =
					typeof this.historizedVersions[ this.accountID ] !== 'undefined';
			} catch ( error ) {
				this.validAccountID = false;
				this.errorMessage = errors.verification_error;
			}
		},
		editAccountID() {
			this.showID = false;
		},
	};
};

export default {
	instance,
};
