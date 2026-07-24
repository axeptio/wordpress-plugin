const TYPE_BOOLEAN = 'boolean';
const TYPE_STRING = 'string';
const TYPE_NUMBER = 'number';
const TYPE_STRING_LIST = 'string[]';
const TYPE_BOOLEAN_UPDATE = "boolean | 'update_only'";
const TYPE_DURATION = "number | 'page' | 'session'";

const advancedSettings = function( config ) {
	return {
		reference: config.reference || [],
		i18n: config.i18n || {},
		rows: [],
		nextUid: 0,
		openSelect: null,
		focusedProperty: null,
		keySearch: '',

		TYPE_BOOLEAN,
		TYPE_STRING,
		TYPE_NUMBER,
		TYPE_STRING_LIST,
		TYPE_BOOLEAN_UPDATE,
		TYPE_DURATION,

		init() {
			this.rows = ( config.pairs || [] ).map( ( pair ) => this.makeRow( pair ) );
		},

		makeRow( pair = {} ) {
			return {
				_uid: this.nextUid++,
				property: pair.property || '',
				type: pair.type || '',
				value: pair.value === undefined || pair.value === null ? '' : String( pair.value ),
			};
		},

		fieldName( index, key ) {
			return `axeptio_settings[advanced_settings][${ index }][${ key }]`;
		},

		addRow() {
			this.rows.push( this.makeRow() );
			this.$nextTick( () => {
				this.$refs.rows?.lastElementChild
					?.querySelector( '[data-select-trigger]' )
					?.focus();
			} );
		},

		removeRow( index ) {
			this.rows.splice( index, 1 );
			this.closeSelect();
		},

		availableOptions( row ) {
			const used = this.rows
				.filter( ( item ) => item !== row )
				.map( ( item ) => item.property )
				.filter( Boolean );

			return this.reference.filter( ( option ) => ! used.includes( option.property ) );
		},

		filteredOptions( row ) {
			const query = this.keySearch.trim().toLowerCase();
			const options = this.availableOptions( row );

			if ( ! query ) {
				return options;
			}

			return options.filter(
				( option ) =>
					option.name.toLowerCase().includes( query ) ||
					option.property.toLowerCase().includes( query ) ||
					( option.description || '' ).toLowerCase().includes( query )
			);
		},

		selectFirst( row ) {
			const first = this.filteredOptions( row )[ 0 ];
			if ( first ) {
				this.selectProperty( row, first.property );
			}
		},

		findOption( property ) {
			return this.reference.find( ( option ) => option.property === property );
		},

		keyLabel( row ) {
			const option = this.findOption( row.property );
			return option ? option.name : row.property;
		},

		isUnknownSetting( row ) {
			return !! row.property && ! this.findOption( row.property );
		},

		descriptionOf( row ) {
			const option = this.findOption( row.property );
			return option ? option.description : '';
		},

		placeholderFor( row ) {
			if ( row.type === TYPE_STRING_LIST ) {
				return this.i18n.list_placeholder || '';
			}

			const option = this.findOption( row.property );
			const fallback = option ? option.default : null;

			return fallback === null || fallback === undefined ? '' : String( fallback );
		},

		onPropertyChange( row ) {
			const option = this.findOption( row.property );
			row.type = option ? option.type : TYPE_STRING;
			row.value = this.defaultValueForType( row.type );
		},

		defaultValueForType( type ) {
			if ( type === TYPE_BOOLEAN ) {
				return '0';
			}
			if ( type === TYPE_BOOLEAN_UPDATE ) {
				return 'false';
			}
			return '';
		},

		toggleBoolean( row ) {
			row.value = row.value === '1' ? '0' : '1';
		},

		durationMode( row ) {
			if ( row.value === 'page' || row.value === 'session' ) {
				return row.value;
			}
			return 'days';
		},

		setDurationMode( row, mode ) {
			row.value = mode === 'days' ? '' : mode;
		},

		toggleSelect( key ) {
			this.openSelect = this.openSelect === key ? null : key;
			this.keySearch = '';
			this.focusedProperty = null;
		},

		isOpen( key ) {
			return this.openSelect === key;
		},

		closeSelect() {
			this.openSelect = null;
			this.keySearch = '';
		},

		selectProperty( row, property ) {
			row.property = property;
			this.onPropertyChange( row );
			this.closeSelect();
		},

		selectValue( row, value ) {
			row.value = value;
			this.closeSelect();
		},

		selectDurationMode( row, mode ) {
			this.setDurationMode( row, mode );
			this.closeSelect();
		},

		labelFor( options, value ) {
			const match = ( options || [] ).find( ( option ) => option.value === value );
			return match ? match.label : '';
		},
	};
};

export default advancedSettings;
