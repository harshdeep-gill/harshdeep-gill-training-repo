/**
 * Global variables
 */
const { HTMLElement, zustand } = window;

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

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

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the state.
		const { resultCount } = state;

		// Get the button text element.
		const buttonTextElement = this.theButton?.querySelector( '.dates-rates__apply-filters-btn-count' );

		// Null check.
		if ( ! buttonTextElement ) {
			// Bail.
			return;
		}

		// Set the text content.
		buttonTextElement.textContent = `(${ resultCount })`;
	}

	/**
	 * Handles the click on `Apply Filters` button.
	 */
	handleApplyFilters() {
		// close the drawer.
		this.theButton?.closest<QuarkDrawerElement>( 'quark-drawer' )?.close();
	}
}
