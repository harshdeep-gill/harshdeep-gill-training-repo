/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

// TODO: Add comment.
import { TPMultiSelectElement } from '@travelopia/web-components';
/**
 * Internal dependencies.
 */
import { getCurrency, setCurrency } from '../../global/store/actions';

/**
 * CurrencySwitcher Class.
 */
export class CurrencySwitcher extends HTMLElement {
	/**
	 * Properties.
	 */
	private currencyDropdown: TPMultiSelectElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Elements.
		this.currencyDropdown = this.querySelector( 'tp-multi-select' );

		// Events.
		this.currencyDropdown?.addEventListener( 'change', this.updateCurrencyState.bind( this ) );

		// Initialize.
		this.initialize();
	}

	/**
	 * Initialize component.
	 */
	initialize() {
		// If currency dropdown is not available.
		if ( ! this.currencyDropdown ) {
			// Bail out.
			return;
		}

		// Get currency from state.
		const currency = getCurrency();

		// Set currency to dropdown.
		this.currencyDropdown?.select( currency );
	}

	/**
	 * Update currency state.
	 */
	updateCurrencyState() {
		// If currency dropdown is not available.
		if ( ! this.currencyDropdown ) {
			// Bail out.
			return;
		}

		// Get the currency dropdown value.
		const currency = this.currencyDropdown?.value[ 0 ] ?? 'USD';

		// Set currency to cookie.
		setCurrency( currency );
	}
}

// Define custom element.
customElements.define( 'quark-currency-switcher', CurrencySwitcher );
