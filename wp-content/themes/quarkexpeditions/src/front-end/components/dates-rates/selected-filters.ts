/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Get the store.
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Internal Dependencies
 */
import { updateFilters } from './actions';

/**
 * Selected Filters Class.
 */
export default class DatesRatesSelectedFiltersElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly selectedFilterPillTemplate: HTMLTemplateElement | null;
	private readonly filtersList: HTMLElement | null;
	private readonly clearAllButton: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize parent.
		super();

		// Initialize properties.
		this.selectedFilterPillTemplate = this.querySelector( 'template' );
		this.filtersList = this.querySelector( '.dates-rates__selected-filters-list' );
		this.clearAllButton = this.querySelector( '.dates-rates__selected-filters-clear-all' );

		// Set up events.
		this.clearAllButton?.addEventListener( 'click', this.handleClearAll.bind( this ) );

		// Subscribe to store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update the component.
	 *
	 * @param {Object} state
	 */
	update( state: DatesRatesState ) {
		// Get the selected filters.
		const { selectedFilters } = state;
		let areAnyFiltersSelected = false;

		// Loop through the selected filters.
		for ( const filterName in selectedFilters ) {
			// Check if currency.
			if ( 'currency' === filterName ) {
				// Do nothing.
				continue;
			}

			// Get the selected values for the filter.
			const selectedValues = new Set<string>( selectedFilters[ filterName ] );
			const filterInputsContainer = document.getElementById( `filters-accordion-${ filterName.slice( 0 ).replace( '_', '-' ) }` );

			// Check if any filters are selected.
			if ( selectedValues.size > 0 ) {
				areAnyFiltersSelected = true;
			}

			// Get the filter pills.
			const relevantFilterPills = this.querySelectorAll( 'quark-dates-rates-selected-filter-pill' );

			// Loop through the filter pills and remove unnecessary ones.
			relevantFilterPills.forEach( ( pill ) => {
				// Get the value.
				const pillValue = pill.getAttribute( 'value' ) ?? '';
				const pillFilter = pill.getAttribute( 'filter' ) ?? '';

				// Check if the pill should exist.
				if (
					( pillValue && selectedValues.has( pillValue ) ) ||
					( pillFilter && pillFilter !== filterName )
				) {
					// Delete the value from the set.
					selectedValues.delete( pillValue );

					// Bail.
					return;
				}

				// Remove the child.
				this.filtersList?.removeChild( pill );
				selectedValues.delete( pillValue );
			} );

			// Loop through the values and create new pills.
			selectedValues.forEach( ( value: string ) => {
				// Clone the template.
				const filterPillTemplateClone = this.selectedFilterPillTemplate?.content.cloneNode( true ) as HTMLElement | undefined;
				const filterPill = filterPillTemplateClone?.querySelector( 'quark-dates-rates-selected-filter-pill' );

				// Null Check.
				if ( ! filterPill ) {
					// Bail.
					return;
				}

				// Set relevant attributes.
				filterPill?.setAttribute( 'filter', filterName );
				filterPill?.setAttribute( 'value', value );

				// Get relevant information.
				const filterPillTextElement = filterPill?.querySelector( '.dates-rates__selected-filter-text' );
				const relevantInputLabel = filterInputsContainer
					?.querySelector( `input[value="${ value }"]` )
					?.parentElement
					?.querySelector( 'label' )
					?.textContent
					?.replace( /[^a-zA-Z\d\s:]/gi, '' ); // to remove non alpha-num characters.

				// Null check.
				if ( filterPillTextElement ) {
					// Set the inner html.
					filterPillTextElement.textContent = relevantInputLabel ?? '';
				}

				// Append the child.
				this.filtersList?.appendChild( filterPill );
			} );
		}

		// Check if selected filters should be hidden.
		if ( areAnyFiltersSelected ) {
			this.removeAttribute( 'empty' );
		} else {
			this.setAttribute( 'empty', 'yes' );
		}
	}

	/**
	 * Handle the click on Clear All button.
	 */
	handleClearAll() {
		// Update the filters.
		updateFilters( {
			seasons: [],
			expeditions: [],
			adventure_options: [],
			durations: [],
			months: [],
			ships: [],
			currency: 'USD',
		} );
	}
}
