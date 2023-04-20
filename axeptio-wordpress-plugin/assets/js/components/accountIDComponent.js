const instance = function( args ) {
	return {
		axeptioSettings: {},
		accountID: args.accountID,
		showID: false,
		errorMessage: '',
		validAccountID: false,
		activeSDK: args.activeSDK,
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
		async validateAccountID() {
			this.errorMessage = '';

			if ( this.accountID.trim() === '' ) {
				this.validAccountID = false;
				this.errorMessage = this.axeptioSettings.errors.empty_account_id;
				return;
			}
			try {
				const response = await fetch( `https://client.axept.io/${ this.accountID }.json` );
				const data = await response.json();
				if ( data.cookies.length > 0 ) {
					this.showID = true;
					this.options = data.cookies.map( ( cookie ) => ( {
						value: cookie.title,
						text: cookie.title,
					} ) );
					this.validAccountID = true;
					this.optionsJson = JSON.stringify( this.options );
				} else {
					this.validAccountID = false;
					this.errorMessage = this.axeptioSettings.errors.non_existing_account_id;
				}
			} catch ( error ) {
				this.validAccountID = false;
				this.errorMessage = this.axeptioSettings.errors.verification_error;
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
