const instance = function( args ) {
	const repeaterMethods = {
		sanitizeArray( originalArray ) {
			return originalArray.filter( function( item ) {
				return item.trim() !== '';
			} );
		},

		refreshRepeaterFields() {
			const self = this;
			[ 'wp_filter_list', 'shortcode_tags_list' ].forEach( ( fieldSlug ) => {
				self.initRepeaterFields( fieldSlug );
				self.updateRepeaterField( fieldSlug );
			} );
		},

		getFields( fieldSlug ) {
			return this.fields[ fieldSlug ];
		},

		initRepeaterFields( fieldSlug ) {
			if ( this.editedPlugin.Name === '' ) {
				return;
			}
			this.inputRefs[ fieldSlug ] = [];

			this.fields[ fieldSlug ] = this.sanitizeArray( this.editedPlugin.Metas[ fieldSlug ].split( '\n' ) );

			if ( this.fields[ fieldSlug ].length === 0 || ( this.fields[ fieldSlug ].length === 1 && this.fields[ fieldSlug ][ 0 ] === '' ) ) {
				this.fields[ fieldSlug ].push( '' );
			}
		},

		storeRef( fieldSlug, el, index ) {
			this.inputRefs[ fieldSlug ][ index ] = el;
			this.$watch( 'editedPlugin', () => {
				this.$nextTick( () => {
					if ( typeof this.inputRefs[ fieldSlug ] === 'undefined' ) {
						this.inputRefs[ fieldSlug ] = [];
					}
					this.inputRefs[ fieldSlug ][ index ] = el;
				} );
			} );
		},

		addField( fieldSlug, index = this.fields[ fieldSlug ].length ) {
			this.fields[ fieldSlug ].splice( index, 0, '' );
			this.$nextTick( () => {
				this.$refs.scrollContainer.scrollTop = this.$refs.scrollContainer.scrollHeight;
				this.inputRefs[ fieldSlug ][ index ].focus();
			} );
		},

		removeFieldAndFocusPrevious( fieldSlug, index ) {
			if ( this.fields[ fieldSlug ][ index ] === '' ) {
				this.removeField( fieldSlug, index );
				this.$nextTick( () => {
					const previousIndex = index - 1 >= 0 ? index - 1 : 0;
					this.inputRefs[ fieldSlug ][ previousIndex ].focus();
				} );
			}
		},

		removeField( fieldSlug, index ) {
			this.fields[ fieldSlug ].splice( index, 1 );
			if ( this.fields[ fieldSlug ].length === 0 ) {
				this.addField( fieldSlug );
			}
			this.updateRepeaterField( fieldSlug );
		},

		updateRepeaterField( fieldSlug ) {
			if ( typeof this.fields[ fieldSlug ] === 'undefined' ) {
				this.fields[ fieldSlug ] = [];
			}
			this.editedPlugin.Metas[ fieldSlug ] = this.fields[ fieldSlug ].join( '\n' );
		},
	};

	const pluginMethods = {
		fetchPlugins() {
			this.isGetting = true;
			const apiUrl = `/wp-json/axeptio/v1/plugins/${ this.configurationId }`;
			fetch( apiUrl, {
				method: 'GET',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': args.nonce,
				},
			} )
				.then( ( response ) => response.json() )
				.then( ( data ) => {
					this.plugins = data;
					this.isGetting = false;
				} );
		},

		deletePlugin( plugin ) {
			this.isSaving = true;
			const apiUrl = `/wp-json/axeptio/v1/plugins/${ this.configurationId }/${ plugin.Metas.plugin }`;
			fetch( apiUrl, {
				method: 'DELETE',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': this.nonce,
				},
				body: JSON.stringify( plugin.Metas ),
			} )
				.then( ( response ) => response.json() )
				.then( () => {
					this.isSaving = false;
					this.editOpen = false;
					this.closeDeleteModal();
					this.fetchPlugins();
					this.editedPlugin.Metas.enabled = false;
					this.setForceEditOpen( false );
				} );
		},

		setHasChanged() {
			if ( this.editOpen ) {
				this.editedPluginHasChanged = true;
			}
		},

		isActive( tab ) {
			return tab === this.activeTab;
		},

		setActive( value ) {
			this.activeTab = value;
		},

		editPlugin( plugin ) {
			this.setActive( 1 );
			this.editOpen = true;
			this.editedPlugin = plugin;
			this.hookModes = plugin.HookModes;
			this.shortcodeTagsModes = plugin.ShortcodeTagsModes;

			this.refreshRepeaterFields();
		},

		updatePlugin( plugin ) {
			this.isSaving = true;

			const apiUrl = `/wp-json/axeptio/v1/plugins/${ this.configurationId }/${ plugin.Metas.plugin }`;
			fetch( apiUrl, {
				method: 'PUT',
				headers: {
					'Content-Type': 'application/json',
					'X-WP-Nonce': this.nonce,
				},
				body: JSON.stringify( plugin.Metas ),
			} )
				.then( ( response ) => response.json() )
				.then( () => {
					this.refreshRepeaterFields();
					this.isSaving = false;
					this.editedPluginHasChanged = false;
				} );
		},

		openMediaSelector() {
			this.setForceEditOpen( true );
			const self = this;
			const customUploader = wp.media( {
				title: wp.i18n.__( 'Select a media', 'axeptio-wordpress-plugin' ),
				button: { text: wp.i18n.__( 'Use this media', 'axeptio-wordpress-plugin' ) },
				multiple: false,
			} ).on( 'select', function() {
				const attachment = customUploader.state().get( 'selection' ).first().toJSON();
				self.editedPlugin.Metas.vendor_image = attachment.url;
				self.setForceEditOpen( false );
			} ).open();
		},

		localOrGlobalEnabled( plugin ) {
			return this.localEnabled( plugin ) || this.globalEnabled( plugin );
		},

		globalEnabled( plugin ) {
			if ( plugin.Metas.enabled !== false || typeof plugin.Metas.Parent === 'undefined' ) {
				return false;
			}

			return Boolean( plugin.Metas.Parent.enabled );
		},

		localEnabled( plugin ) {
			return plugin.Metas.enabled === 1;
		},

		enableControl( plugin ) {
			const { Metas } = plugin;

			this.editedPlugin = plugin;
			Metas.enabled = ! Boolean( Metas.enabled ) ? 1 : 0;
			Metas.cookie_widget_step = 'wordpress';
			this.updatePlugin( plugin );
		},

		initEditedPlugin() {
			this.editedPlugin = {
				Name: '',
				Metas: {
					wp_filter_mode: 'none',
					shortcode_tags_mode: 'none',
					wp_filter_list: '',
					shortcode_tags_list: '',
				},
			};
		},

	};

	const deleteModal = {
		openDeleteModal() {
			this.setForceEditOpen( true );
			this.showDeleteModal = true;
		},

		closeDeleteModal() {
			this.showDeleteModal = false;
			this.setForceEditOpen( false );
		},

		confirmDelete( editedPlugin ) {
			this.deletePlugin( editedPlugin );
		},
	};

	const localizedMetaMethods = {
		getLocalizedMeta( field, lang ) {
			if ( ! this.editedPlugin || ! this.editedPlugin.Metas ) {
				return '';
			}

			const value = this.editedPlugin.Metas[ field ];

			if ( ! value || value === '' ) {
				return '';
			}

			try {
				const parsed = JSON.parse( value );
				if ( typeof parsed === 'object' && parsed !== null ) {
					return parsed[ lang ] || '';
				}
			} catch ( e ) {
				// Plain string (legacy non-JSON format): treat as default language value.
				return lang === 'default' ? value : '';
			}

			// JSON.parse succeeded but result is not an object (e.g. a plain string was valid JSON).
			return lang === 'default' ? value : '';
		},

		setLocalizedMeta( field, lang, value ) {
			if ( ! this.editedPlugin || ! this.editedPlugin.Metas ) {
				return;
			}

			if ( lang === 'default' ) {
				this.editedPlugin.Metas[ field ] = value;
				this.setHasChanged();
				return;
			}

			const currentValue = this.editedPlugin.Metas[ field ] || '';
			let parsed = {};

			if ( currentValue ) {
				try {
					const temp = JSON.parse( currentValue );
					if ( typeof temp === 'object' && temp !== null ) {
						parsed = temp;
					} else {
						// Valid JSON but not an object: preserve as default value.
						parsed = { default: currentValue };
					}
				} catch ( e ) {
					// Plain string (legacy format): preserve as default language value.
					parsed = { default: currentValue };
				}
			}

			parsed[ lang ] = value;

			const hasNonEmptyValue = Object.values( parsed ).some( ( v ) => v && v.trim() !== '' );
			this.editedPlugin.Metas[ field ] = hasNonEmptyValue ? JSON.stringify( parsed ) : '';
			this.setHasChanged();
		},
	};

	return {
		...repeaterMethods,
		...deleteModal,
		...pluginMethods,
		...localizedMetaMethods,
		plugins: [],
		editedPlugin: null,
		editedPluginHasChanged: false,
		configurationId: 'all',
		activeTab: 1,
		currentPage: 1,
		forceEditOpen: false,
		activePlugins: args.active_plugins,
		totalPages: 1,
		nonce: args.nonce,
		isSaving: false,
		isGetting: false,
		hookModes: [],
		projectVersions: args.project_versions,
		shortcodeTagsModes: [],
		editOpen: false,
		showDeleteModal: false,
		pluginToDelete: null,
		inputRefs: [],
		fields: [],

		// prevent from close the edit panel when click inside the media selector
		setForceEditOpen( enabled ) {
			if ( enabled ) {
				this.forceEditOpen = true;
			} else {
				setTimeout( () => {
					this.forceEditOpen = false;
				} );
			}
		},

		closePanel() {
			if ( this.forceEditOpen ) {
				return;
			}
			this.editOpen = false;
			if ( this.editedPluginHasChanged ) {
				this.fetchPlugins();
				this.editedPluginHasChanged = false;
			}
		},

		init() {
			this.initEditedPlugin();
			this.fetchPlugins();
		},
	};
};

export default {
	instance,
};
