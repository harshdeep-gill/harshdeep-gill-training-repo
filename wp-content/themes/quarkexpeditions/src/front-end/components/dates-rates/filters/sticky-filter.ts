/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Get the store.
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Sticky filter Class.
 */
export default class DatesRatesFilterStickyFilterElement extends HTMLElement {
	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Subscribe to store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the selected filters.
		const { selectedFilters } = state;

		//  Is there a filter?
		let isFiltered = false;

		// Loop through the object.
		for ( const filterName in selectedFilters ) {
			// Check if it is the currency filter.
			if ( 'currency' === filterName ) {
				// Do nothing.
				continue;
			}

			// Check if any filter is active.
			if ( selectedFilters[ filterName ] && selectedFilters[ filterName ].length ) {
				isFiltered = true;
				break;
			}
		}

		// Check if it is filtered.
		if ( isFiltered ) {
			// Yes, add attribute.
			this.setAttribute( 'filtered', 'yes' );
		} else {
			// No, remove attribute.
			this.removeAttribute( 'filtered' );
		}
	}
}
