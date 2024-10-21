/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Internal dependencies.
 */
import { MonthsMultiSelect } from '../../months-multi-select/main';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.searchFiltersBar;

/**
 * Class SearchFilterDepartureMonths.
 */
export class SearchFilterDepartureMonths extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchFiltersModal: HTMLElement | null;
	private destinationFilters: HTMLElement | null | undefined;
	private departureMonthsFilters: HTMLElement | null | undefined;
	private departureMonthsSelectors: NodeListOf<MonthsMultiSelect> | null | undefined;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Subscribe.
		subscribe( this.update.bind( this ) );

		// Elements.
		this.searchFiltersModal = document.querySelector( '.search-filters-bar__modal' );
		this.destinationFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__destinations-filter-options' );
		this.departureMonthsFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__departure-months-filter-options' );
		this.departureMonthsSelectors = this.searchFiltersModal?.querySelectorAll( 'quark-months-multi-select' );

		// Event Listeners.
		this.addEventListener( 'click', this.handleFilterClick.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: SearchFiltersBarState ): void {
		// Get state.
		const { departureMonthOptions } = state;

		// Set month options.
		if ( departureMonthOptions.length > 0 ) {
			this.refreshFilters( departureMonthOptions );
		}
	}

	/**
	 * Handle Departure Months Filter Click.
	 */
	handleFilterClick() {
		// Check if the elements exist.
		if ( ! this.destinationFilters || ! this.departureMonthsFilters ) {
			// Bail early.
			return;
		}

		// Update active state of the filters in the modal.
		this.departureMonthsFilters?.setAttribute( 'active', 'true' );
		this.destinationFilters?.setAttribute( 'active', 'false' );
	}

	/**
	 * Refresh the departure month filters.
	 *
	 * @param {Array} options Available Options.
	 */
	refreshFilters( options: Array<object> ) {
		// Check if options exist.
		if ( ! options ) {
			// Bail.
			return;
		}

		// Set the available months attribute for month selectors.
		this.departureMonthsSelectors?.forEach( ( monthSelector ) => {
			// Set filter options.
			monthSelector?.setAttribute( 'available-months', JSON.stringify( options ) );
		} );
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar-departure-months', SearchFilterDepartureMonths );
