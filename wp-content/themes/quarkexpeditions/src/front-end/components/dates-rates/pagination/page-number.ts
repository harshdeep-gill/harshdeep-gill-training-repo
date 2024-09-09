/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { setPage } from '../actions';

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Page Number class.
 */
export default class DatesRatesPaginationPageNumberElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly theButton: HTMLButtonElement | null;
	private pageNumber: number;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.theButton = this.querySelector( 'button' );
		this.pageNumber = parseInt( this.getAttribute( 'number' ) ?? '' );

		// Do we have an invalid button?
		if ( Number.isNaN( this.pageNumber ) ) {
			// Yes, bail.
			return;
		}

		// Event.
		this.theButton?.addEventListener( 'click', this.handleClick.bind( this ) );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the page number.
		const { page, totalPages } = state;

		// Null check.
		if ( ! this.theButton ) {
			// Bail.
			return;
		}

		// Check if it is the current page.
		if ( page === this.pageNumber ) {
			this.theButton.classList.add( 'current' );
		} else {
			this.theButton.classList.remove( 'current' );
		}

		// Check if this is the `last` link.
		if ( this.theButton.classList.contains( 'pagination__last-page' ) ) {
			// Update accordingly.
			this.pageNumber = totalPages;
			this.setAttribute( 'number', totalPages.toString() );
		}
	}

	/**
	 * Handles the click event.
	 */
	handleClick() {
		// Is this a prev button?
		setPage( this.pageNumber );
	}
}
