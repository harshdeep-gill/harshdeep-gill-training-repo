/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { setNextPage } from '../actions';

/**
 * Store
 */
const { subscribe, getState } = zustand.stores.datesRates;

/**
 * Links controller class.
 */
export default class DatesRatesPaginationLinksControllerElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly pageNumberTemplate: HTMLTemplateElement | null;
	private readonly theButton: HTMLButtonElement | null;
	private isNextButton: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.pageNumberTemplate = this.querySelector( '.dates-rates__template-pagination-page' );

		// Do we have an invalid button?
		if ( ! this.isNextButton ) {
			// Yes, bail.
			return;
		}

		// Event.
		this.theButton?.addEventListener( 'click', this.handleClick.bind( this ) );

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
		// Get the page number.
		const { page, totalPages } = state;

		// Null check.
		if ( ! this.theButton ) {
			// Bail.
			return;
		}

		/**
		 * Check if we should hide the button.
		 */
		if ( page === totalPages ) {
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
		// Is this a next button?
		setNextPage();
	}
}
