/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Total Pages class.
 */
export default class DatesRatesPaginationTotalPagesElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly totalPagesElement: HTMLElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.totalPagesElement = this.querySelector( '.pagination__total-pages' );

		// There's no element for output.
		if ( ! this.totalPagesElement ) {
			// Bail.
			return;
		}

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
		if ( ! this.totalPagesElement ) {
			// Bail.
			return;
		}

		// Get the page number.
		const { page, totalPages } = state;

		// Setup output string.
		const outputString = `Page ${ page } of ${ totalPages }`;

		// Output the string.
		this.totalPagesElement.textContent = outputString;
	}
}
