/**
 * Global variables
 */
const { HTMLElement } = window;

/**
 * External Dependencies.
 */
import { QuarkDrawerElement } from '../../drawer/drawer';

/**
 * View results CTA class.
 */
export default class DatesRatesFiltersCtaViewResultsElement extends HTMLElement {
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
		this.theButton?.addEventListener( 'click', this.handleApplyFilters.bind( this ) );
	}

	/**
	 * Handles the click on `Apply Filters` button.
	 */
	handleApplyFilters() {
		// close the drawer.
		this.theButton?.closest<QuarkDrawerElement>( 'quark-drawer' )?.close();
	}
}
