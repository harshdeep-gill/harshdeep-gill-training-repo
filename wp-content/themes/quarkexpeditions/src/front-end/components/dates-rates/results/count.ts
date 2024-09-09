/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Results Count Class
 */
export default class DatesRatesResultsCountElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly resultCountElement: HTMLElement | null;
	private readonly totalResultsCountElement: HTMLElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.resultCountElement = this.querySelector( '.dates-rates__result-count-value' );
		this.totalResultsCountElement = this.querySelector( '.dates-rates__result-count-total' );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Null check.
		if ( ! this.totalResultsCountElement || ! this.resultCountElement ) {
			// Bail.
			return;
		}

		// Get the state
		const { resultCount, perPage } = state;

		// Set output.
		this.totalResultsCountElement.textContent = resultCount.toString();
		this.resultCountElement.textContent = ( perPage >= resultCount ? resultCount : perPage ).toString();
	}
}
