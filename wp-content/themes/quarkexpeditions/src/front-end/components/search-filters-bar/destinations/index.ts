/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { SearchFilterDestinationOption } from './filter-option';

/**
 * Class SearchFilterDestinations.
 */
export class SearchFilterDestinations extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchFiltersModal: HTMLElement | null;
	private destinationFiltersContainer: HTMLElement | null | undefined;
	private departureMonthsFiltersContainer: HTMLElement | null | undefined;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super
		super();

		// Elements.
		this.searchFiltersModal = document.querySelector( '.search-filters-bar__modal' );
		this.destinationFiltersContainer = this.searchFiltersModal?.querySelector( '.search-filters-bar__destinations-filter-options' );
		this.departureMonthsFiltersContainer = this.searchFiltersModal?.querySelector( '.search-filters-bar__departure-months-filter-options' );

		// Event Listeners.
		this.addEventListener( 'click', this.handleFilterClick.bind( this ) );
	}

	/**
	 * Handle Destinations Filter Click.
	 */
	handleFilterClick() {
		// Check if the elements exist.
		if ( ! this.destinationFiltersContainer || ! this.departureMonthsFiltersContainer ) {
			// Bail early.
			return;
		}

		// Update active state of the filters in the modal.
		this.destinationFiltersContainer?.setAttribute( 'active', 'true' );
		this.departureMonthsFiltersContainer?.setAttribute( 'active', 'false' );
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
		const styledOptions: NodeListOf<SearchFilterDestinationOption> | null = this.querySelectorAll( 'tp-multi-select-option' );
		styledOptions?.forEach( ( option: SearchFilterDestinationOption ): void => {
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
		const selectedOptions: NodeListOf<SearchFilterDestinationOption> | undefined = this.searchFiltersModal?.querySelectorAll( 'quark-search-filters-bar-destinations-option[selected="yes"]' );
		selectedOptions?.forEach( ( option: SearchFilterDestinationOption ) => {
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
		// If single select.
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
		const options: NodeListOf<SearchFilterDestinationOption> | undefined = this.searchFiltersModal?.querySelectorAll( `quark-search-filters-bar-destinations-option[value="${ value }"]` );
		options?.forEach( ( option: SearchFilterDestinationOption ): void => {
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
		const allOptionsWithValue: NodeListOf<SearchFilterDestinationOption> | undefined = this.searchFiltersModal?.querySelectorAll( `quark-months-multi-select-option[value="${ value }"]` );

		// Loop through all options with the matching value.
		allOptionsWithValue?.forEach( ( option: SearchFilterDestinationOption ): void => {
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
		const allOptions: NodeListOf<SearchFilterDestinationOption> | undefined = this.searchFiltersModal?.querySelectorAll( 'quark-search-filters-bar-destinations-option' );
		allOptions?.forEach( ( option: SearchFilterDestinationOption ): void => {
			// Remove selected attribute.
			option.removeAttribute( 'selected' );
		} );

		// Dispatch change event.
		this.dispatchEvent( new CustomEvent( 'change' ) );
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar-destinations', SearchFilterDestinations );
