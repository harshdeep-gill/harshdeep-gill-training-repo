/**
 * Global variables
 */
const { HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { clearAllFilters } from '../actions';

/**
 * Filters controller class.
 */
export default class DatesRatesFiltersControllerElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly applyFiltersButton: HTMLButtonElement | null;
	private readonly clearAllButton: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize parent properties.
		super();

		/**
		 * Initialize filters.
		 *
		 * These need to be queried on the document because these
		 * are inside a drawer which is pulled out of the parent on initialization.
		 */
		this.applyFiltersButton = document.querySelector( '.dates-rates__apply-filters-btn' );
		this.clearAllButton = document.querySelector( '.dates-rates__cta-clear-filters' );

		// Add Event Listeners
		this.applyFiltersButton?.addEventListener( 'click', this.handleApplyFilters.bind( this ) );
		this.clearAllButton?.addEventListener( 'click', this.handleClearAll.bind( this ) );
	}

	/**
	 * Handles the click on `Apply Filters` button.
	 */
	handleApplyFilters() {
		// Update the state.
	}

	/**
	 * Handle the click on Clear All button.
	 */
	handleClearAll() {
		// Update the filters.
		clearAllFilters();
	}
}
