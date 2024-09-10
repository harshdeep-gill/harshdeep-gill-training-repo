/**
 * Globals
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Dates Rates Class
 */
export default class DatesRates extends HTMLElement {
	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the loading state.
		const { isLoading } = state;

		// Check and set loading attribute.
		if ( isLoading ) {
			this.setAttribute( 'loading', 'yes' );
		} else {
			this.removeAttribute( 'loading' );
		}
	}
}

// Define the element.
customElements.define( 'quark-dates-rates', DatesRates );
