/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * External dependency.
 */
import { TPMultiSelectElement } from '@travelopia/web-components';

/**
 * Internal dependencies.
 */
import { updateCurrency } from '../actions';

/**
 * Get the store.
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Currency Dropdown filter Class.
 */
export default class DatesRatesFilterCurrencyDropdownElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly currencySelector: TPMultiSelectElement | null;
	private isSyncing: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.currencySelector = this.querySelector( 'tp-multi-select' );
		this.isSyncing = false;

		// Set up events.
		this.currencySelector?.addEventListener( 'change', this.handleCurrencySelectorChange.bind( this ) );

		// Subscribe to the store
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state
	 */
	update( state: DatesRatesState ) {
		// Get the currency filter value.
		const { currency } = state;

		// Set syncing
		this.isSyncing = true;

		// Check and update currency selector.
		if ( currency && this.currencySelector ) {
			// Select the appropriate value.
			this.currencySelector.select( currency );
		}

		// Unset syncing.
		this.isSyncing = false;
	}

	/**
	 * Handles the change event for the currency dropdown.
	 */
	handleCurrencySelectorChange() {
		// Check if the currency dropdown is null.
		if ( ! this.currencySelector || this.isSyncing ) {
			// bail.
			return;
		}

		// Get the updated value.
		const currencyValue = this.currencySelector.value[ 0 ] ?? 'USD';

		// Update the state.
		updateCurrency( currencyValue );
	}
}
