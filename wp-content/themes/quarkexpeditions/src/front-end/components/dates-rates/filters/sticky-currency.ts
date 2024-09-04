/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Get the store.
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Currency Sticky filter Class.
 */
export default class DatesRatesFilterStickyCurrencyElement extends HTMLElement {
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
	 * Updates component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the currency filter value.
		const { currency, areCurrencyFiltersSyncing } = state;

		// Check if we should sync.
		if ( ! areCurrencyFiltersSyncing ) {
			// No, we should not.
			return;
		}

		// Get the button text container.
		const buttonTextContainer = this.querySelector( '.btn__content-text' );

		// Null check.
		if ( ! ( buttonTextContainer && currency ) ) {
			// Bail.
			return;
		}

		// Update the currency markup.
		buttonTextContainer.innerHTML = `Currency: ${ currency }`;
	}
}
