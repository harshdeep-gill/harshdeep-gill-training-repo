/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { setPreviousPage } from '../actions';

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Previous Page class.
 */
export default class DatesRatesPaginationPrevPageElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly theButton: HTMLButtonElement | null;
	private isPrevButton: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.theButton = this.querySelector( 'button' );
		this.isPrevButton = this.theButton?.classList.contains( 'prev' ) ?? false;

		// Do we have an invalid button?
		if ( ! this.isPrevButton ) {
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
		const { page } = state;

		// Null check.
		if ( ! this.theButton ) {
			// Bail.
			return;
		}

		/**
		 * Check if we should hide the button.
		 */
		if ( page === 1 ) {
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
		setPreviousPage();
	}
}
