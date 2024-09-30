/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependency.
 */
import { TPMultiSelectElement } from '@travelopia/web-components';

/**
 * Internal dependencies.
 */
import { setCurrency } from '../../../global/store/actions';

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
		setCurrency( currencyValue );
	}
}
