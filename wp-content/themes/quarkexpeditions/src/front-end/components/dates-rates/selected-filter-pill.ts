/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Get the store.
 */
const { getState } = zustand.stores.datesRates;

/**
 * Internal Dependencies
 */
import { updateFilters } from './actions';

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
		// Get the state.
		const { selectedFilters }: DatesRatesState = getState();
		const updatedFilters = { ...selectedFilters };

		// Get the attributes.
		const pillFilter = this.getAttribute( 'filter' ) ?? '';
		const pillValue = this.getAttribute( 'value' );

		// Set the updated filters.
		updatedFilters[ pillFilter ] = updatedFilters[ pillFilter ].filter( ( value: string ) => value !== pillValue );

		// Set filters state.
		updateFilters( updatedFilters );
	}
}
