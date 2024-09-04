/**
 * Global variables
 */
const { HTMLElement } = window;

/**
 * External Dependencies.
 */
import { QuarkDrawerElement } from '../../drawer/drawer';

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
		 * Initialize properties.
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
		// close the drawer.
		this.applyFiltersButton?.closest<QuarkDrawerElement>( 'quark-drawer' )?.close();
	}

	/**
	 * Handle the click on Clear All button.
	 */
	handleClearAll() {
		// Update the filters.
		clearAllFilters();

		// close the drawer.
		this.clearAllButton?.closest<QuarkDrawerElement>( 'quark-drawer' )?.close();
	}
}
