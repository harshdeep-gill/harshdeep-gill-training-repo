/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { MonthsMultiSelect } from '../../months-multi-select/main';

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
	private departureMonthsSelector: MonthsMultiSelect | null | undefined;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super
		super();

		// Elements.
		this.searchFiltersModal = document.querySelector( '.search-filters-bar__modal' );
		this.destinationFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__destinations-filter-options' );
		this.departureMonthsFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__departure-months-filter-options' );
		this.departureMonthsSelector = this.searchFiltersModal?.querySelector( 'quark-months-multi-select' );

		// Event Listeners.
		this.addEventListener( 'click', this.handleFilterClick.bind( this ) );
		this.departureMonthsSelector?.addEventListener( 'change', this.refreshFilters.bind( this ) );
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

	// TODO: Add comment.
	refreshFilters() {
		// Call action to make API Call to refresh filters.
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar-departure-months', SearchFilterDepartureMonths );
