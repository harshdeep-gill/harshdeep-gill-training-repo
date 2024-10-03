/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Get the store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * Internal dependencies
 */
import { clearAllFilters } from '../actions';

/**
 * ExpeditionSearchSelectedFilters Class.
 */
export default class ExpeditionSearchSelectedFilters extends HTMLElement {
	private readonly selectedFiltersList: HTMLElement | null;
	private readonly selectedFilterPillTemplate: HTMLTemplateElement | null;
	private readonly clearAllButton: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.selectedFiltersList = this.querySelector( '.expedition-search__selected-filters-list' );
		this.selectedFilterPillTemplate = this.querySelector( 'template' );
		this.clearAllButton = this.querySelector( '.expedition-search__selected-filters-clear-all' );

		// Set up events.
		this.clearAllButton?.addEventListener( 'click', this.handleClearAll.bind( this ) );

		// Subscribe to store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param { Object } state The state object.
	 */
	update( state: ExpeditionSearchState ) {
		// Null check.
		if ( ! this.selectedFiltersList || ! this.selectedFilterPillTemplate ) {
			// Bail.
			return;
		}

		// Are any filters selected?
		let areAnyFiltersSelected = false;

		// The filter keys.
		const searchFilters: { [ key: string ]: string } = {
			destinations: 'Destination',
			months: 'Departure',
			itineraryLengths: 'Itinerary Length',
			ships: 'Ship',
			adventureOptions: 'Adventure Option',
			languages: 'Language',
			expeditions: 'Expedition',
			cabinClasses: 'Cabin Class',
			travelers: 'Travelers',
		};

		// Check if any filters are selected.
		for ( const key in searchFilters ) {
			// @ts-ignore Check the length and break if it is more than 0.
			if ( Array.isArray( state[ key ] ) && state[ key ].length > 0 ) {
				areAnyFiltersSelected = true;
				break;
			}
		}

		// Reset the markup.
		this.selectedFiltersList.innerHTML = '';

		// Are any filters selected?
		if ( ! areAnyFiltersSelected ) {
			// Hide this component.
			this.setAttribute( 'data-hidden', '' );

			// Bail.
			return;
		}

		// Loop through the filters and generate the pills.
		Object.keys( searchFilters ).forEach( ( filterKey ) => {
			// @ts-ignore Get the selected values.
			const selectedValues: ExpeditionSearchFilterState[] = state[ filterKey ];
			const filterName = searchFilters[ filterKey ];

			// Sanity check.
			if ( ! Array.isArray( selectedValues ) ) {
				// Bail.
				return;
			}

			// Loop through the selected values.
			selectedValues.forEach( ( selectedValue ) => {
				// Clone the template.
				const filterPillTemplateClone = this.selectedFilterPillTemplate?.content.cloneNode( true ) as HTMLElement | undefined;
				const filterPill = filterPillTemplateClone?.querySelector( 'quark-expedition-search-selected-filter-pill' );

				// Null Check.
				if ( ! filterPill || ! selectedValue.value || ! selectedValue.label ) {
					// Bail.
					return;
				}

				// Set relevant attributes.
				filterPill?.setAttribute( 'filter', filterKey );
				filterPill?.setAttribute( 'value', selectedValue.value );

				// Get relevant information.
				const filterPillTextElement = filterPill.querySelector( '.expedition-search__selected-filter-text' );

				// Null check.
				if ( filterPillTextElement && selectedValue.label ) {
					// Set the inner html.
					filterPillTextElement.textContent = `${ filterName }: ${ selectedValue.label }`;
				}

				// Append the pill.
				this.selectedFiltersList?.appendChild( filterPill );
			} );
		} );

		// Show this component.
		this.removeAttribute( 'data-hidden' );
	}

	/**
	 * Handle the click on Clear All button.
	 */
	handleClearAll() {
		// Update the filters.
		clearAllFilters();
	}
}
