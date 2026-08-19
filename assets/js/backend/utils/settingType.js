/**
 * Resolves a declared type into a primitive and its literal members, so a union
 * such as `boolean | 'forced'` gets an editable input instead of failing an exact
 * string comparison.
 *
 * Mirrors class-setting-type.php, held to tests/fixtures/setting-types.json.
 */

export const BOOLEAN = 'boolean';
export const NUMBER = 'number';
export const STRING = 'string';

const STRING_LIST = 'string[]';

export const WIDGET_TOGGLE = 'toggle';
export const WIDGET_SELECT = 'select';
export const WIDGET_MODE = 'mode';
export const WIDGET_NUMBER = 'number';
export const WIDGET_LIST = 'list';
export const WIDGET_TEXT = 'text';

/** Stands for "type a number" next to the literals of a numeric union. */
export const MODE_NUMBER = '__number__';

const PRIMITIVES = [ BOOLEAN, NUMBER, STRING, STRING_LIST ];
const TRUTHY = [ '1', 'true', 'on', 'yes' ];
const FALSY = [ '0', 'false', 'off', 'no' ];

/** What PHP's is_numeric() accepts, so both sides agree on a number. */
const NUMERIC = /^[+-]?(\d+(\.\d*)?|\.\d+)([eE][+-]?\d+)?$/;

const resolved = new Map();

/**
 * Read a declared type, member by member.
 *
 * @param {string} type Declared setting type.
 * @return {{primitive: ?string, literals: string[]}} Primitive and literals.
 */
function split( type ) {
	const literals = [];
	let primitive = null;

	type.split( '|' ).forEach( ( member ) => {
		const value = member.trim();
		const literal = value.match( /^'(.*)'$/ );

		if ( literal ) {
			literals.push( literal[ 1 ] );
			return;
		}

		if ( primitive === null && PRIMITIVES.includes( value ) ) {
			primitive = value;
		}
	} );

	return { primitive, literals };
}

/**
 * Pick the input to render. A type holding no known primitive falls back to a
 * text field, so a setting is never left without a way to enter its value.
 *
 * @param {?string} primitive Resolved primitive.
 * @param {boolean} union     Whether the type declares literals.
 * @return {string} One of the WIDGET_* constants.
 */
function widgetOf( primitive, union ) {
	if ( primitive === BOOLEAN ) {
		return union ? WIDGET_SELECT : WIDGET_TOGGLE;
	}

	if ( primitive === NUMBER ) {
		return union ? WIDGET_MODE : WIDGET_NUMBER;
	}

	if ( primitive === STRING_LIST && ! union ) {
		return WIDGET_LIST;
	}

	return WIDGET_TEXT;
}

/**
 * Determine whether a value fits a primitive. An unresolved one accepts
 * anything: the merchant edits it as text.
 *
 * @param {?string} primitive Resolved primitive.
 * @param {string}  value     Value to check.
 * @return {boolean} True when the value fits.
 */
function fitsPrimitive( primitive, value ) {
	if ( primitive === BOOLEAN ) {
		const normalized = value.toLowerCase();
		return TRUTHY.includes( normalized ) || FALSY.includes( normalized );
	}

	if ( primitive === NUMBER ) {
		return NUMERIC.test( value.trim() );
	}

	if ( primitive === STRING_LIST ) {
		return value.split( ',' ).some( ( item ) => item.trim() !== '' );
	}

	return true;
}

/**
 * Resolve a declared type once: everything derivable from it is computed here,
 * and Alpine re-evaluates the template expressions on every update.
 *
 * @param {string} type Declared setting type.
 * @return {Object} Frozen resolved type.
 */
function build( type ) {
	const { primitive, literals } = split( type );

	return Object.freeze( {
		primitive,
		literals,
		widget: widgetOf( primitive, literals.length > 0 ),

		// Booleans start on the "off" side so a toggle always has a state;
		// every other type starts empty and has to be filled in.
		defaultValue: primitive === BOOLEAN ? '0' : '',

		isLiteral: ( value ) => literals.includes( String( value ) ),

		// An empty value is never accepted: it would be cast to `0`, `false` or
		// an empty string and override the key in window.axeptioSettings.
		accepts: ( value ) => {
			const raw = value === undefined || value === null ? '' : String( value );

			if ( raw === '' ) {
				return false;
			}

			return literals.includes( raw ) || fitsPrimitive( primitive, raw );
		},
	} );
}

/**
 * @param {string} type Declared setting type.
 * @return {Object} Resolved type, shared between callers.
 */
export default function settingType( type ) {
	const key = String( type || '' );

	if ( ! resolved.has( key ) ) {
		resolved.set( key, build( key ) );
	}

	return resolved.get( key );
}
