/**
 * Global variables
 */
const { HTMLElement } = window;

/**
 * External Dependencies.
 */
import { QuarkDrawerElement } from '../../drawer/drawer';
import { clearAllFilters } from '../actions';

/**
 * Clear filters CTA class.
 */
export default class DatesRatesFiltersCtaClearElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly theButton: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize parent properties.
		super();

		/**
		 * Initialize properties.
		 */
		this.theButton = this.querySelector( '.btn' );

		// Add Event Listeners
		this.theButton?.addEventListener( 'click', this.handleClearAll.bind( this ) );
	}

	/**
	 * Handles the click on `Clear All` button.
	 */
	handleClearAll() {
		// Clear all filters.
		clearAllFilters();

		// close the drawer.
		this.theButton?.closest<QuarkDrawerElement>( 'quark-drawer' )?.close();
	}
}
