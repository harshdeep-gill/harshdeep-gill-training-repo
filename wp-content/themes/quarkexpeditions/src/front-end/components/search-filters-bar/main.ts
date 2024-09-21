/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Class SearchFiltersBar.
 */
export class SearchFiltersBar extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchFiltersModal: HTMLElement | null;
	private searchModalDestinationsButton: HTMLElement | null;
	private searchModalDeparturesButton: HTMLElement | null;
	private destinationFilters: HTMLElement | null | undefined;
	private departureMonthsFilters: HTMLElement | null | undefined;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Elements.
		this.searchFiltersModal = document.querySelector( '.search-filters-bar__modal' );
		this.searchModalDestinationsButton = this?.querySelector( '.search-filters-bar__modal-open-button-destinations' );
		this.searchModalDeparturesButton = this?.querySelector( '.search-filters-bar__modal-open-button-departures' );
		this.destinationFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__destinations-filter-options' );
		this.departureMonthsFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__departure-months-filter-options' );

		// Event Listeners.
		this.searchModalDestinationsButton?.addEventListener(
			'click',
			this.toggleDestinationFilterOptions.bind( this )
		);
		this.searchModalDeparturesButton?.addEventListener(
			'click',
			this.toggleDepartureFilterOptions.bind( this )
		);
	}

	/**
	 * Toggle destination filter options.
	 */
	toggleDestinationFilterOptions() {
		// Check if the elements exist.
		if ( ! this.destinationFilters || ! this.departureMonthsFilters ) {
			// Bail early.
			return;
		}

		// Toggle the filter active state.
		this.destinationFilters?.setAttribute( 'active', 'true' );
		this.departureMonthsFilters?.setAttribute( 'active', 'false' );
	}

	/**
	 * Toggle departure filter options.
	 */
	toggleDepartureFilterOptions() {
		// Check if the elements exist.
		if ( ! this.destinationFilters || ! this.departureMonthsFilters ) {
			// Bail early.
			return;
		}

		// Toggle the filter active state.
		this.departureMonthsFilters?.setAttribute( 'active', 'true' );
		this.destinationFilters?.setAttribute( 'active', 'false' );
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar', SearchFiltersBar );
