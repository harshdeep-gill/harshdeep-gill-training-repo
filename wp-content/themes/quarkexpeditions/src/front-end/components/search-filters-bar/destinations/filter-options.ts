/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.searchFiltersBar;

/**
 * Class SearchFilterDestinationOptions.
 */
export class SearchFilterDestinationOptions extends HTMLElement {
	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Subscribe.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State
	 */
	update( state: SearchFiltersBarState ): void {
		// Get state.
		const { destinationOptions } = state;

		// Set month options.
		if ( destinationOptions ) {
			this.refreshFilters( destinationOptions );
		}
	}

	/**
	 * Refresh the destination options to display relevant filters.
	 *
	 * @param {Array} options Available Options.
	 */
	refreshFilters( options: Array<object> ) {
		// Check if options exist.
		if ( ! options ) {
			// Bail.
			return;
		}

		// Set the destinations attribute.
		this?.setAttribute( 'destinations', JSON.stringify( options ) );
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar-destinations-options', SearchFilterDestinationOptions );
