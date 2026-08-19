import settingType, {
	BOOLEAN,
	MODE_NUMBER,
	NUMBER,
	STRING,
	WIDGET_LIST,
	WIDGET_MODE,
	WIDGET_NUMBER,
	WIDGET_SELECT,
	WIDGET_TEXT,
	WIDGET_TOGGLE,
} from '../utils/settingType';

const ERROR_PROPERTY = 'property';
const ERROR_VALUE = 'value';
const ERROR_INVALID = 'invalid';
const ERROR_URL = 'url';

/**
 * The reference declares no format, so the `…Url` suffix is the only signal.
 *
 * @param {string} property Property name.
 * @return {boolean} True when the value has to be a URL.
 */
function expectsUrl( property ) {
	return property.toLowerCase().endsWith( 'url' );
}

/**
 * Only absolute http(s) URLs are accepted, mirroring Advanced_Settings::is_url().
 *
 * @param {string} value Value to check.
 * @return {boolean} True when the value is an absolute http(s) URL.
 */
function isUrl( value ) {
	try {
		const { protocol, hostname } = new URL( value );

		if ( protocol !== 'http:' && protocol !== 'https:' ) {
			return false;
		}

		return hostname.includes( '.' );
	} catch {
		return false;
	}
}

const advancedSettings = function( config ) {
	return {
		reference: config.reference || [],
		i18n: config.i18n || {},
		tab: config.tab,
		rows: [],
		nextUid: 0,
		openSelect: null,
		focusedProperty: null,
		keySearch: '',
		showErrors: false,

		WIDGET_TOGGLE,
		WIDGET_SELECT,
		WIDGET_MODE,
		WIDGET_NUMBER,
		WIDGET_LIST,
		WIDGET_TEXT,
		MODE_NUMBER,

		init() {
			this.rows = ( config.pairs || [] ).map( ( pair ) => this.makeRow( pair ) );

			this.$el
				.closest( 'form' )
				?.addEventListener( 'submit', ( event ) => this.onSubmit( event ) );
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

		addRowHint() {
			if ( this.rows.some( ( row ) => ! row.property ) ) {
				return this.i18n.errors.pending_row;
			}

			if ( ! this.availableOptions().length ) {
				return this.i18n.errors.all_used;
			}

			return '';
		},

		canAddRow() {
			return ! this.addRowHint();
		},

		addRow() {
			if ( ! this.canAddRow() ) {
				return;
			}

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

		widget( row ) {
			return settingType( row.type ).widget;
		},

		placeholderFor( row ) {
			if ( this.widget( row ) === WIDGET_LIST ) {
				return this.i18n.list_placeholder || '';
			}

			const option = this.findOption( row.property );
			const fallback = option ? option.default : null;

			return fallback === null || fallback === undefined ? '' : String( fallback );
		},

		/**
		 * Build the entries of a value select from the resolved type. A literal
		 * with no translation shows as-is, so one added by Axeptio stays usable
		 * without a plugin release.
		 *
		 * @param {Object} row Row being edited.
		 * @return {Array<{value: string, label: string}>} Select entries.
		 */
		optionsFor( row ) {
			const { primitive, literals } = settingType( row.type );
			const options = [];

			if ( primitive === BOOLEAN ) {
				options.push( { value: '1', label: this.i18n.bool.on } );
				options.push( { value: '0', label: this.i18n.bool.off } );
			}

			if ( primitive === NUMBER ) {
				options.push( {
					value: MODE_NUMBER,
					label: this.i18n.number_modes?.[ row.property ] || this.i18n.number_mode,
				} );
			}

			literals.forEach( ( literal ) => {
				options.push( {
					value: literal,
					label: this.i18n.literals?.[ literal ] || literal,
				} );
			} );

			return options;
		},

		onPropertyChange( row ) {
			const option = this.findOption( row.property );
			row.type = option ? option.type : STRING;
			row.value = settingType( row.type ).defaultValue;
		},

		toggleBoolean( row ) {
			row.value = row.value === '1' ? '0' : '1';
		},

		modeOf( row ) {
			return settingType( row.type ).isLiteral( row.value ) ? row.value : MODE_NUMBER;
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

		selectMode( row, mode ) {
			row.value = mode === MODE_NUMBER ? '' : mode;
			this.closeSelect();
		},

		labelFor( options, value ) {
			const match = ( options || [] ).find( ( option ) => option.value === value );
			return match ? match.label : '';
		},

		rowError( row ) {
			if ( ! row.property ) {
				return ERROR_PROPERTY;
			}

			if ( row.value === '' ) {
				return ERROR_VALUE;
			}

			if ( ! settingType( row.type ).accepts( row.value ) ) {
				return ERROR_INVALID;
			}

			if ( expectsUrl( row.property ) && ! isUrl( row.value ) ) {
				return ERROR_URL;
			}

			return '';
		},

		errorFor( row ) {
			const error = this.showErrors ? this.rowError( row ) : '';

			return error ? this.i18n.errors[ error ] : '';
		},

		/**
		 * Hold the save back while a row is incomplete. The tab is switched
		 * first: the panel is hidden behind it, so from any other tab the save
		 * button would just look inert.
		 *
		 * @param {SubmitEvent} event Form submission.
		 */
		onSubmit( event ) {
			const index = this.rows.findIndex( ( row ) => this.rowError( row ) !== '' );

			if ( index < 0 ) {
				return;
			}

			event.preventDefault();
			this.showErrors = true;
			this.currentTab = this.tab;
			this.$nextTick( () => this.focusRow( index ) );
		},

		focusRow( index ) {
			// The x-for template sits in the same parent, so rows are read by
			// element rather than by child index.
			const element = this.$refs.rows?.querySelectorAll( ':scope > div' )[ index ];

			if ( ! element ) {
				return;
			}

			const target =
				this.rowError( this.rows[ index ] ) === ERROR_PROPERTY
					? null
					: element.querySelector( '[data-value-input]' );

			element.scrollIntoView( { block: 'center', behavior: 'smooth' } );
			( target || element.querySelector( '[data-select-trigger]' ) )?.focus();
		},
	};
};

export default advancedSettings;
