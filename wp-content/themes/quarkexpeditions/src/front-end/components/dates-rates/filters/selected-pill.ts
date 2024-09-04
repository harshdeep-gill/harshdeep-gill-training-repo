/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * Internal Dependencies
 */
import { removeFilterCreator } from '../actions';

/**
 * Selected Filter Pill Class.
 */
export default class DatesRatesSelectedFilterPillElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly closeButton: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize parent.
		super();

		// Initialize properties.
		this.closeButton = this.querySelector( '.dates-rates__selected-filter-close' );

		// Setup Events.
		this.closeButton?.addEventListener( 'click', this.handleClosePill.bind( this ) );
	}

	/**
	 * Handles the close pill action.
	 */
	handleClosePill() {
		// Get the attributes.
		const pillFilter = this.getAttribute( 'filter' ) ?? '';
		const pillValue = this.getAttribute( 'value' ) ?? '';

		// Set filters state.
		removeFilterCreator( pillFilter )( pillValue );
	}
}
