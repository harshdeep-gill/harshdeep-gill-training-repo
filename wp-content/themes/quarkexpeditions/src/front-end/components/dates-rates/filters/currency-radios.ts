/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { setCurrency } from '../../../global/store/actions';

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
		setCurrency( currencyValue );
	}
}
