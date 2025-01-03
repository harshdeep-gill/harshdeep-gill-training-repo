/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * External Dependencies
 */
import { TPMultiSelectElement } from '@travelopia/web-components';

/**
 * Internal Dependencies.
 */
import { setAllowedPerPage, setPerPage } from '../actions';

/**
 * Store
 */
const { subscribe, getState } = zustand.stores.datesRates;

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

		// Set allowed per page filter values
		this.updateAllowedPerPage();

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
		// Null check.
		if ( ! this.itemsPerPageSelector || ! this.itemsPerPageSelector.select ) {
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

	/**
	 * Updates the allowedPerPage state.
	 */
	updateAllowedPerPage() {
		// Get the state.
		const { allowedPerPage }: DatesRatesState = getState();

		// Has it already been set?
		if ( allowedPerPage.length || ! this.itemsPerPageSelector ) {
			// Bail.
			return;
		}

		// New value init.
		const newAllowedPerPage: number[] = [];

		// Get the options and populate the array.
		this.itemsPerPageSelector.querySelectorAll( 'tp-multi-select-option' ).forEach( ( opt ) => {
			// Get the value
			const optValue = parseInt( opt.getAttribute( 'value' ) ?? '' );

			// Do we have an invalue number?
			if ( Number.isNaN( optValue ) || optValue < 1 ) {
				// yes, return.
				return;
			}

			// Push the value in the array.
			newAllowedPerPage.push( optValue );
		} );

		// Set the state.
		setAllowedPerPage( newAllowedPerPage );
	}
}
