const instance = function( args ) {
	return {
		timeoutButton: null,
		disableButton: null,
		nonce: args.nonce,

		init() {
			this.timeoutButton = this.$el.querySelector( '#axeptio-timeout-button' );
			this.disableButton = this.$el.querySelector( '#axeptio-disable-button' );

			this.bind();

			this.timeoutButton.addEventListener( 'click', this.handleTimeout );
			this.disableButton.addEventListener( 'click', this.handleDisable );
		},

		bind() {
			this.handleTimeout = this.handleTimeout.bind( this );
			this.handleDisable = this.handleDisable.bind( this );
		},

		handleTimeout( e ) {
			e.preventDefault();
			this.$el.remove();
			this.fetchAPI( 'timeout-notice' );
		},

		handleDisable( e ) {
			e.preventDefault();
			this.$el.remove();
			this.fetchAPI( 'disable-notice' );
		},

		fetchAPI( route ) {
			const apiUrl = `/wp-json/axeptio/v1/${ route }`;

			fetch( apiUrl, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': this.nonce,
				},
			} ).then( ( response ) => response.json() );
		},
	};
};

export default {
	instance,
};
