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
import { clearAllFilters } from '../actions';

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
		let areAnyFiltersSelected = false;

		// Get the selected filters.
		const { selectedFilters } = state;

		// Null check.
		if ( ! this.filtersList ) {
			// Bail.
			return;
		}

		// Reset selected filter chips markup.
		this.filtersList.innerHTML = '';

		// Loop through the filters.
		selectedFilters.forEach( ( selectedFilter ) => {
			// Get the selected values.
			const selectedValues: DatesRatesFilterState[] = state[ selectedFilter ];
			const filterName = selectedFilter;

			// Check if any filters are selected.
			if ( selectedValues.length > 0 ) {
				areAnyFiltersSelected = true;
			}

			// Loop through the values and create new pills.
			selectedValues.forEach( ( selectedValue: DatesRatesFilterState ) => {
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
				filterPill?.setAttribute( 'value', selectedValue.value );

				// Get relevant information.
				const filterPillTextElement = filterPill?.querySelector( '.dates-rates__selected-filter-text' );

				// Null check.
				if ( filterPillTextElement ) {
					// Set the inner html.
					filterPillTextElement.textContent = selectedValue.label;
				}

				// Append the child.
				this.filtersList?.appendChild( filterPill );
			} );
		} );

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
		clearAllFilters();
	}
}
