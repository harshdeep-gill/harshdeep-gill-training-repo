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
	static observedAttributes = [ 'available-months' ];

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Elements.
		this.resetButtons = this.querySelectorAll( '.months-multi-select__reset-button' );
		this.availableMonths = JSON.parse( this.getAttribute( 'available-months' ) ?? '' );

		// Event Listeners.
		this.addEventListener( 'select', this.toggleAllResetButtons.bind( this, 'off' ) );
		this.addEventListener( 'unselect', this.toggleAllResetButtons.bind( this, 'on' ) );

		// Reset buttons.
		if ( this.resetButtons ) {
			this.resetButtons.forEach( ( buttonElement ) => {
				// Add event listener to the reset button.
				buttonElement?.addEventListener( 'click', this.resetSelector.bind( this ) );
				buttonElement?.addEventListener( 'click', this.toggleActiveAttribute.bind( this ) );
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
		if ( 'available-months' !== name || oldValue === newValue ) {
			// Nope, bail.
			return;
		}

		// Disable unavailable month options.
		this.disableUnavailableMonthOptions( JSON.parse( this.getAttribute( 'available-months' ) ?? '' ) );
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
		this.unSelectAll();

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

		// Set the value of the select field.
		const allOptions: NodeListOf<MonthsMultiSelectOption> | null = this.querySelectorAll( 'tp-multi-select-option' );

		// Loop through all options.
		allOptions?.forEach( ( option: MonthsMultiSelectOption ): void => {
			// Check if the value is in the array.
			if ( value.includes( option.getAttribute( 'value' ) ?? '' ) ) {
				option.setAttribute( 'selected', 'yes' );
			} else {
				option.removeAttribute( 'selected' );
			}
		} );

		// Dispatch change event.
		this.dispatchEvent( new CustomEvent( 'change' ) );
	}

	/**
	 * Get the value of this component.
	 *
	 * @return {Set} Value of this component.
	 */
	get value(): Set<string> {
		// Get the value of the select field.
		const value = new Set<string>();

		// Get selected options.
		const selectedOptions: NodeListOf<MonthsMultiSelectOption> | null = this.querySelectorAll( 'quark-months-multi-select-option[selected="yes"]' );
		selectedOptions?.forEach( ( option: MonthsMultiSelectOption ) => {
			// Get option value.
			const optionValue = option.getAttribute( 'value' );

			// Add value to set.
			if ( optionValue ) {
				value.add( optionValue );
			}
		} );

		// Return value.
		return value;
	}

	/**
	 * Select a value.
	 *
	 * @param {string} value Value to select.
	 */
	select( value: string = '' ): void {
		// Stuff for single-select.
		if ( 'no' === this.getAttribute( 'multi-select' ) ) {
			// First, unselect everything silently.
			this.unSelectAll( true );

			// If the value is blank, don't do anything else.
			if ( '' === value ) {
				// Exit.
				return;
			}
		}

		// Select the option.
		const options: NodeListOf<MonthsMultiSelectOption> | null = this.querySelectorAll( `quark-months-multi-select-option[value="${ value }"]` );
		options?.forEach( ( option: MonthsMultiSelectOption ): void => {
			// Update select field.
			if ( 'yes' !== option.getAttribute( 'disabled' ) ) {
				option.setAttribute( 'selected', 'yes' );
			}
		} );

		// Dispatch change event.
		this.dispatchEvent( new CustomEvent( 'change' ) );
	}

	/**
	 * Un-select a value.
	 *
	 * @param {string} value Value to unselect.
	 */
	unSelect( value: string = '' ): void {
		// Get all options with the specified value.
		const allOptionsWithValue: NodeListOf<MonthsMultiSelectOption> | null = this.querySelectorAll( `quark-months-multi-select-option[value="${ value }"]` );

		// Loop through all options with the matching value.
		allOptionsWithValue?.forEach( ( option: MonthsMultiSelectOption ): void => {
			// Remove selected attribute.
			option.removeAttribute( 'selected' );
		} );

		// Dispatch change event.
		this.dispatchEvent( new CustomEvent( 'change' ) );
	}

	/**
	 * Un-select all values.
	 *
	 * @param { boolean } silent Should the unselect happen without triggering change event?
	 */
	unSelectAll( silent: boolean = false ): void {
		// Get all options.
		const allOptions: NodeListOf<MonthsMultiSelectOption> | null = this.querySelectorAll( 'quark-months-multi-select-option' );
		allOptions?.forEach( ( option: MonthsMultiSelectOption ): void => {
			// Remove selected attribute.
			option.removeAttribute( 'selected' );
		} );

		// Check if silent
		if ( ! silent ) {
			this.dispatchEvent( new CustomEvent( 'change', { bubbles: true } ) );
		}
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
