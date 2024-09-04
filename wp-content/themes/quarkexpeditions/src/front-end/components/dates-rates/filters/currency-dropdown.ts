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
const { subscribe, getState } = zustand.stores.datesRates;

/**
 * Currency Dropdown filter Class.
 */
export default class DatesRatesFilterCurrencyDropdownElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly currencySelector: TPMultiSelectElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.currencySelector = this.querySelector( 'tp-multi-select' );

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
		const { selectedFilters: { currency }, areCurrencyFiltersSyncing } = state;

		// Check if we should sync.
		if ( ! areCurrencyFiltersSyncing ) {
			// No, we should not.
			return;
		}

		// Check and update currency selector.
		if ( currency && this.currencySelector ) {
			// Select the appropriate value.
			this.currencySelector.select( currency );
		}
	}

	/**
	 * Handles the change event for the currency dropdown.
	 */
	handleCurrencySelectorChange() {
		// Check if currencyFilters are syncing.
		const { areCurrencyFiltersSyncing }: DatesRatesState = getState();

		// Check if the currency dropdown is null.
		if ( ! this.currencySelector || areCurrencyFiltersSyncing ) {
			// bail.
			return;
		}

		// Get the updated value.
		const currencyValue = this.currencySelector.value[ 0 ] ?? 'USD';

		// Update the state.
		updateCurrency( currencyValue );
	}
}
