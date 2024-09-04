/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies.
 */
import { updateCurrency } from '../actions';

/**
 * Get the store.
 */
const { subscribe, getState } = zustand.stores.datesRates;

/**
 * Currency Radios Filter class
 */
export default class DatesRatesFilterCurrencyRadiosElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly currencyRadios: NodeListOf<HTMLInputElement>;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize Properties
		this.currencyRadios = this.querySelectorAll( 'input[type="radio"]' );

		// Events.
		this.currencyRadios.forEach( ( currencyRadioInput ) => currencyRadioInput.addEventListener( 'change', this.handleCurrencyRadioChange.bind( this ) ) );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the currency filter value.
		const { selectedFilters: { currency }, areCurrencyFiltersSyncing } = state;

		// Check if we should update.
		if ( ! ( areCurrencyFiltersSyncing && currency ) ) {
			// No, we should not.
			return;
		}

		// Loop through the radio inputs.
		this.currencyRadios.forEach( ( radioInput ) => {
			// Check for proper value.
			if ( radioInput.value !== currency ) {
				// Bail.
				return;
			}

			// Set the radio to checked.
			radioInput.checked = true;
		} );
	}

	/**
	 * Handles the change event for the currency radio inputs.
	 *
	 * @param { Event } event The event object.
	 */
	handleCurrencyRadioChange( event: Event ) {
		// Check if currencyFilters are syncing.
		const { areCurrencyFiltersSyncing }: DatesRatesState = getState();

		// Null check.
		if ( ! event.target || areCurrencyFiltersSyncing ) {
			// bail.
			return;
		}

		// Get the radio input.
		const theRadioInput = event.target as HTMLInputElement;

		// Is the radio checked.
		if ( ! theRadioInput.checked ) {
			// No, bail.
			return;
		}

		// Get the currency value.
		const currencyValue = theRadioInput.value !== 'on' ? theRadioInput.value : 'USD';

		// Update the state.
		updateCurrency( currencyValue );
	}
}
