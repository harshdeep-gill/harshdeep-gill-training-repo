/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Class SearchFilterDestinations.
 */
export class SearchFilterDestinations extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchFiltersModal: HTMLElement | null;
	private destinationFilters: HTMLElement | null | undefined;
	private departureMonthsFilters: HTMLElement | null | undefined;

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

		// Event Listeners.
		this.addEventListener( 'click', this.handleFilterClick.bind( this ) );
	}

	/**
	 * Handle Destinations Filter Click.
	 */
	handleFilterClick() {
		// Check if the elements exist.
		if ( ! this.destinationFilters || ! this.departureMonthsFilters ) {
			// Bail early.
			return;
		}

		// Update active state of the filters in the modal.
		this.destinationFilters?.setAttribute( 'active', 'true' );
		this.departureMonthsFilters?.setAttribute( 'active', 'false' );
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar-destinations', SearchFilterDestinations );
