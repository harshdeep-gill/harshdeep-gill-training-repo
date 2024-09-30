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
		//  Is there a filter?
		let isFiltered = false;

		// Loop through the object.
		for ( const filterName of [ 'seasons', 'expeditions', 'adventureOptions', 'months', 'durations', 'ships' ] ) {
			// @ts-ignore, Check if any filter is active.
			if ( state[ filterName ] && state[ filterName ].length ) {
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
