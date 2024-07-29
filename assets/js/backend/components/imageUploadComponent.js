// imageUploadComponent.js

const instance = function(args) {
	return {
		useImage: args.initialValue !== '',
		imageUrl: args.initialValue,
		fieldName: args.fieldName,
		fieldId: args.fieldId,

		init() {
			this.$watch('useImage', (value) => {
				if (!value) {
					this.imageUrl = '';
				}
			});
		},

		openMediaUploader() {
			if (typeof wp !== 'undefined' && wp.media) {
				const frame = wp.media({
					title: 'Select or Upload Media',
					button: {
						text: 'Use this media'
					},
					multiple: false
				});

				frame.on('select', () => {
					const attachment = frame.state().get('selection').first().toJSON();
					this.imageUrl = attachment.url;
				});

				frame.open();
			}
		},

		removeImage() {
			this.imageUrl = '';
		}
	};
};

export default {
	instance,
};
