/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { setNextPage, setPage, setPreviousPage } from '../actions';

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
	private readonly pageNumber: number;
	private isNextButton: boolean;
	private isPrevButton: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.theButton = this.querySelector( 'button' );
		this.pageNumber = parseInt( this.getAttribute( 'number' ) ?? '' );
		this.isPrevButton = this.theButton?.classList.contains( 'prev' ) ?? false;
		this.isNextButton = this.theButton?.classList.contains( 'next' ) ?? false;

		// Do we have an invalid button?
		if ( ( Number.isNaN( this.pageNumber ) && ! this.isPrevButton && ! this.isNextButton ) ) {
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

		// Check if we have a valid page number.
		if ( ! Number.isNaN( this.pageNumber ) ) {
			// Check if it is the current page.
			if ( page === this.pageNumber ) {
				this.theButton.classList.add( 'current' );
			} else {
				this.theButton.classList.remove( 'current' );
			}
		} else if (
			( this.isPrevButton && page === 1 ) ||
			( this.isNextButton && page === totalPages )
		) {
			this.setAttribute( 'data-hidden', '' );
			this.theButton.disabled = true;
		} else {
			this.removeAttribute( 'data-hidden' );
			this.theButton.disabled = false;
		}
	}

	/**
	 * Handles the click event.
	 */
	handleClick() {
		// Is this a prev button?
		if ( ! Number.isNaN( this.pageNumber ) ) {
			setPage( this.pageNumber );
		} else if ( this.isPrevButton ) {
			setPreviousPage();
		} else if ( this.isNextButton ) {
			setNextPage();
		}
	}
}
