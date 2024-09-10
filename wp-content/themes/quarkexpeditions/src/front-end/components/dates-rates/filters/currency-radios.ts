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
const { subscribe } = zustand.stores.datesRates;

/**
 * Currency Radios Filter class
 */
export default class DatesRatesFilterCurrencyRadiosElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly currencyRadios: NodeListOf<HTMLInputElement>;
	private isSyncing: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize Properties
		this.currencyRadios = this.querySelectorAll( 'input[type="radio"]' );
		this.isSyncing = false;

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
		const { currency } = state;

		// Set syncing
		this.isSyncing = true;

		// Loop through the radio inputs.
		this.currencyRadios.forEach( ( radioInput ) => {
			// Check for proper value.
			if ( radioInput.value !== currency ) {
				// Uncheck the input.
				radioInput.checked = false;

				// Bail.
				return;
			}

			// Set the radio to checked.
			radioInput.checked = true;
		} );

		// Unset syncing
		this.isSyncing = false;
	}

	/**
	 * Handles the change event for the currency radio inputs.
	 *
	 * @param { Event } event The event object.
	 */
	handleCurrencyRadioChange( event: Event ) {
		// Null check.
		if ( ! event.target || this.isSyncing ) {
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
