/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal Dependencies.
 */
import { setTotalCount } from '../actions';

/**
 * Store
 */
const { getState, subscribe } = zustand.stores.datesRates;

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

		// Get the initial state.
		const initialTotalCount = parseInt( this.getAttribute( 'total-count' ) ?? '' );

		// Set the initial total count
		setTotalCount( initialTotalCount );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );

		// Initial sync.
		this.update( getState() );
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
		const { totalItems, perPage } = state;

		// Set output.
		this.totalResultsCountElement.textContent = totalItems.toString();
		this.resultCountElement.textContent = perPage.toString();
	}
}
