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
	private resetButton : HTMLElement | null;
	private availableMonths: Array<object> | null;
	static observedAttributes = [ 'available-months' ];

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Elements.
		this.resetButton = this.querySelector( '.months-multi-select__reset-button' );
		this.availableMonths = JSON.parse( this.getAttribute( 'available-months' ) ?? '' );

		// Event Listeners.
		this.resetButton?.addEventListener( 'click', this.resetSelector.bind( this ) );

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
			// First, unselect everything.
			this.unSelectAll();

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
	 */
	unSelectAll(): void {
		// Get all options.
		const allOptions: NodeListOf<MonthsMultiSelectOption> | null = this.querySelectorAll( 'quark-months-multi-select-option' );
		allOptions?.forEach( ( option: MonthsMultiSelectOption ): void => {
			// Remove selected attribute.
			option.removeAttribute( 'selected' );
		} );

		// Reset value.
		this.value = [];

		// Dispatch change event.
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
