/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * External Dependencies
 */
import { TPMultiSelectElement } from '@travelopia/web-components';
import { setPerPage } from '../actions';

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Items per page class.
 */
export default class DatesRatesPaginationItemsPerPageElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly itemsPerPageSelector: TPMultiSelectElement | null;
	private isSyncing: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.itemsPerPageSelector = this.querySelector( '.pagination__items-per-page-select' );
		this.isSyncing = false;

		// There's no element for output.
		if ( ! this.itemsPerPageSelector ) {
			// Bail.
			return;
		}

		// Events
		this.itemsPerPageSelector.addEventListener( 'change', this.handleChange.bind( this ) );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Null check.
		if ( ! this.itemsPerPageSelector ) {
			// Bail.
			return;
		}

		// Set syncing
		this.isSyncing = true;

		// Get the page number.
		const { perPage } = state;

		// Set the value.
		this.itemsPerPageSelector.select( perPage.toString() );

		// Unset syncing
		this.isSyncing = false;
	}

	/**
	 * Handles the change event.
	 */
	handleChange() {
		// Null check.
		if ( ! this.itemsPerPageSelector || this.isSyncing ) {
			// Bail.
			return;
		}

		// Get the value.
		const value = parseInt( this.itemsPerPageSelector.value[ 0 ] ?? '' );

		// Set the per page value.
		setPerPage( value );
	}
}
