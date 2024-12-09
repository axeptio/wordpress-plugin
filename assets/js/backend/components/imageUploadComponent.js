const instance = function( args ) {
	return {
		disableImage: args.initialValue === 'disabled',
		imageUrl: args.initialValue === 'disabled' ? '' : args.initialValue,
		fieldName: args.fieldName,
		fieldId: args.fieldId,

		init() {
			this.$watch( 'disableImage', ( value ) => {
				if ( value ) {
					this.imageUrl = '';
				}
			} );
		},

		openMediaUploader() {
			if ( typeof wp !== 'undefined' && wp.media ) {
				const frame = wp.media( {
					title: 'Select or Upload Media',
					button: {
						text: 'Use this media',
					},
					multiple: false,
				} );

				frame.on( 'select', () => {
					const attachment = frame.state().get( 'selection' ).first().toJSON();
					this.imageUrl = attachment.url;
					this.disableImage = false;
				} );

				frame.open();
			}
		},

		removeImage() {
			this.imageUrl = '';
		},

		getValue() {
			return this.disableImage ? 'disabled' : this.imageUrl;
		},
	};
};

export default {
	instance,
};
