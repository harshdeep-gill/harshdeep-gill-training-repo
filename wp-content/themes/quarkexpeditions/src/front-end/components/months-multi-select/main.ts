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
		this.resetButton?.addEventListener( 'click', this.unSelectAll.bind( this ) );

		// Disable unavailable month options.
		if ( this.availableMonths ) {
			this.disableUnavailableMonthOptions();
		}
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
		this.dispatchEvent( new CustomEvent( 'change', { bubbles: true } ) );
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
		this.dispatchEvent( new CustomEvent( 'change', { bubbles: true } ) );
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

		// Dispatch change event.
		this.dispatchEvent( new CustomEvent( 'change', { bubbles: true } ) );
	}

	/**
	 * Disable the month options that are not available.
	 */
	disableUnavailableMonthOptions(): void {
		// Get all options with the specified value.
		const allOptions: NodeListOf<MonthsMultiSelectOption> | null = this?.querySelectorAll( 'quark-months-multi-select-option' );

		// Extract the "value" property from each object
		const monthValues = this.availableMonths?.map( ( item: any ) => item?.value );

		// Loop through all options.
		allOptions?.forEach( ( option: MonthsMultiSelectOption ): void => {
			// Set disabled attribute.
			if ( ! monthValues?.includes( option.getAttribute( 'value' ) ) ) {
				option.setAttribute( 'disabled', 'yes' );
			}
		} );
	}
}

// Define Element.
customElements.define( 'quark-months-multi-select', MonthsMultiSelect );
