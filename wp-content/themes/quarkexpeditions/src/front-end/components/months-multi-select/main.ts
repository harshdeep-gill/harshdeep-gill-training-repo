/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { MonthsMultiSelectOption } from './months-multi-select-option';

/**
 * Class MonthsMultiSelect.
 */
export class MonthsMultiSelect extends HTMLElement {
	/**
	 * Properties.
	 */
	private resetButtons : NodeListOf<HTMLElement> | null;
	private availableMonths: Array<object> | null;
	private _value: string[];
	static observedAttributes = [ 'available-months', 'value' ];
	private updatedValueAttribute: boolean;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Elements.
		this.resetButtons = this.querySelectorAll( '.months-multi-select__reset-button' );
		this.availableMonths = JSON.parse( this.getAttribute( 'available-months' ) ?? '' );
		this._value = [];
		this.updatedValueAttribute = false;

		// Get the value attribute.
		const valueAttribute = this.getAttribute( 'value' ) ?? '';

		// Check and assign
		if ( valueAttribute ) {
			// Get the value.
			this.value = valueAttribute.split( ',' ).filter( ( v ) => v !== '' );
		}

		// Event Listeners.
		this.addEventListener( 'change', () => {
			// Check the values.
			if ( this.value.length ) {
				this.toggleAllResetButtons( 'off' );
			} else {
				this.toggleAllResetButtons( 'on' );
			}
		} );

		// Reset buttons.
		if ( this.resetButtons ) {
			this.resetButtons.forEach( ( buttonElement ) => {
				// Add event listener to the reset button.
				buttonElement?.addEventListener( 'click', this.resetSelector.bind( this ) );
			} );
		}

		// Disable unavailable month options.
		if ( this.availableMonths ) {
			this.disableUnavailableMonthOptions( this.availableMonths );
		}
	}

	/**
	 * Responds to attribute change.
	 *
	 * @param { string } name     Attribute name
	 * @param { string } oldValue Old value
	 * @param { string } newValue New value
	 */
	attributeChangedCallback( name: string, oldValue: string, newValue: string ) {
		// Check if available-months attribute.
		if ( ! MonthsMultiSelect.observedAttributes.includes( name ) || oldValue === newValue ) {
			// Nope, bail.
			return;
		}

		// Check and process accordingly.
		if ( 'available-months' === name ) {
			// Disable unavailable month options.
			this.disableUnavailableMonthOptions( JSON.parse( this.getAttribute( 'available-months' ) ?? '' ) );
		} else if ( 'value' === name ) {
			// Check if it was set by this component itself.
			if ( this.updatedValueAttribute ) {
				this.updatedValueAttribute = false;

				// Do nothing.
				return;
			}

			// Get the value.
			this.value = newValue.split( ',' ).filter( ( v ) => v !== '' );
		}
	}

	/**
	 * Toggle active attribute on reset button.
	 *
	 * @param {Event} event Event.
	 */
	toggleActiveAttribute( event: Event ) {
		// Get target.
		const target = event.target as HTMLElement | HTMLButtonElement;

		// Check if target exists and is a button.
		if ( target && target instanceof HTMLButtonElement ) {
			target.toggleAttribute( 'active' );
		} else {
			target.parentElement?.toggleAttribute( 'active' );
		}
	}

	/**
	 * Toggle attribute for all reset buttons.
	 *
	 * @param {string} type Type - on/off.
	 */
	toggleAllResetButtons( type: string ) {
		// If type all then toggle attribute for all buttons.
		this.resetButtons?.forEach( ( button ) => {
			// Toggle active attribute.
			if ( 'on' === type ) {
				button.setAttribute( 'active', '' );
			} else {
				button.removeAttribute( 'active' );
			}
		} );
	}

	/**
	 * Reset selected values.
	 */
	resetSelector() {
		// Unselect all options.
		this.value = [];

		// Dispatch reset custom event.
		this.dispatchEvent( new CustomEvent( 'reset' ) );
	}

	/**
	 * Set the value of this component.
	 *
	 * @param {Array} value Value.
	 */
	set value( value: string[] ) {
		// Bail if value is not an array.
		if ( ! value || ! Array.isArray( value ) ) {
			// Bail early.
			return;
		}

		// Get the values that are not in the new value array ( Essentially, the values that are no longer selected ).
		const oldValue = this._value.filter( ( v ) => ! value.includes( v ) && '' !== v );

		// Get the new values to select.
		const newValue = value.filter( ( v ) => ! this._value.includes( v ) && '' !== v );

		// Is the old array the same as new array?
		if ( oldValue.length === 0 && newValue.length === 0 ) {
			// Yes. bail.
			return;
		}

		// Check if we have some values to unselect.
		if ( oldValue.length ) {
			// Value attribute selector.
			const valueAttributeSelector = oldValue.map( ( val ) => `quark-months-multi-select-option[value="${ val }"]` ).join( ',' );

			// Get the options to unselect.
			this.querySelectorAll( valueAttributeSelector ).forEach( ( opt ) => opt.removeAttribute( 'selected' ) );
		}

		// Set the value
		this._value = this._value.filter( ( v ) => ! oldValue.includes( v ) && ! newValue.includes( v ) && '' !== v );

		// Check if we have some values to select.
		if ( newValue.length ) {
			// Value attribute selector.
			const valueAttributeSelector = newValue.filter( ( val ) => '' !== val ).map( ( val ) => `quark-months-multi-select-option[value="${ val }"][disabled="no"]` ).join( ',' );

			// Check if we have the selector
			if ( valueAttributeSelector !== '' ) {
				// Select the option.
				this.querySelectorAll( valueAttributeSelector ).forEach( ( opt ) => {
					// Add the selected attribute.
					opt.setAttribute( 'selected', 'yes' );

					// Push the value.
					this._value.push( opt.getAttribute( 'value' ) ?? '' );
				} );
			}
		}

		// Set the value attribute
		this.updatedValueAttribute = true;
		this.setAttribute( 'value', this._value.toString() );

		// Dispatch change event.
		this.dispatchEvent( new CustomEvent( 'change' ) );
	}

	/**
	 * Get the value of this component.
	 *
	 * @return {string[]} Value of this component.
	 */
	get value(): string[] {
		// Return value.
		return this._value;
	}

	/**
	 * Select a value.
	 *
	 * @param {string} value Value to select.
	 */
	select( value: string = '' ): void {
		// Stuff for single-select.
		if ( 'no' === this.getAttribute( 'multi-select' ) ) {
			// If the value is blank, don't do anything else.
			if ( '' === value ) {
				// Set the value
				this.value = [];

				// Exit.
				return;
			}

			// Set the value.
			this.value = [ value ];
		} else if ( ! this.value.includes( value ) ) {
			// Check if the value is empty.
			if ( '' === value ) {
				// Bail.
				return;
			}

			// Get the value attribute selector
			const valueAttributeSelector = `quark-months-multi-select-option[value="${ value }"][disabled="no"]`;
			const opt = this.querySelector( valueAttributeSelector );

			// Check if we have the selector
			if ( opt ) {
				// Select the option.
				opt.setAttribute( 'selected', 'yes' );

				// Push the value.
				this.value.push( value );
				this.dispatchEvent( new CustomEvent( 'change' ) );
			}
		}
	}

	/**
	 * Un-select a value.
	 *
	 * @param {string} value Value to unselect.
	 */
	unSelect( value: string = '' ): void {
		// The index of the value.
		const valueIndex = this.value.indexOf( value );

		// The value is already not selected.
		if ( -1 === valueIndex ) {
			// Bail.
			return;
		}

		// Get the value attribute selector
		const valueAttributeSelector = `quark-months-multi-select-option[value="${ value }"]`;
		const opt = this.querySelector( valueAttributeSelector );

		// Null check.
		if ( opt ) {
			opt.removeAttribute( 'selected' );
		}

		// Set the value.
		this.value.splice( valueIndex, 1 );
		this.dispatchEvent( new CustomEvent( 'change' ) );
	}

	/**
	 * Disable the month options that are not available.
	 *
	 * @param {Array} options Options.
	 */
	disableUnavailableMonthOptions( options: Array<object> ): void {
		// Get all options with the specified value.
		const allOptions: NodeListOf<MonthsMultiSelectOption> | null = this?.querySelectorAll( 'quark-months-multi-select-option' );

		// Extract the "value" property from each object
		const monthValues = options?.map( ( item: any ) => item?.value );

		// Loop through all options.
		allOptions?.forEach( ( option: MonthsMultiSelectOption ): void => {
			// Set disabled attribute.
			if ( monthValues?.includes( option.getAttribute( 'value' ) as string ) ) {
				option.setAttribute( 'disabled', 'no' );
			} else {
				option.setAttribute( 'disabled', 'yes' );
				option.setAttribute( 'selected', 'no' );
			}
		} );
	}
}

// Define Element.
customElements.define( 'quark-months-multi-select', MonthsMultiSelect );
