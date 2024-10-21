/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { MonthsMultiSelect } from './main';

/**
 * Class MonthsMultiSelectOption.
 */
export class MonthsMultiSelectOption extends HTMLElement {
	/**
	 * Properties.
	 */

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Event listener.
		this.addEventListener( 'click', this.toggle.bind( this ) );
	}

	/**
	 * Select / un-select this option.
	 *
	 * @param {Event} e Click event.
	 */
	toggle( e: Event | null ): void {
		// Prevent default behavior and stop propagation.
		e?.preventDefault();
		e?.stopPropagation();

		// Get multi-select element and value of option.
		const monthsMultiSelect: MonthsMultiSelect | null = this.closest( 'quark-months-multi-select' );
		const value: string = this.getAttribute( 'value' ) ?? '';

		// Toggle selected state. Dispatch custom events accordingly.
		if ( 'yes' !== this.getAttribute( 'selected' ) && 'yes' !== this.getAttribute( 'disabled' ) ) {
			monthsMultiSelect?.select( value );
			monthsMultiSelect?.dispatchEvent( new CustomEvent( 'select', {
				bubbles: true,
				detail: { value },
			} ) );
		} else {
			monthsMultiSelect?.unSelect( value );
			monthsMultiSelect?.dispatchEvent( new CustomEvent( 'unselect', {
				bubbles: true,
				detail: { value },
			} ) );
		}

		// Dispatch change event.
		monthsMultiSelect?.dispatchEvent( new CustomEvent( 'change', { bubbles: true } ) );
	}
}

// Define Element.
customElements.define( 'quark-months-multi-select-option', MonthsMultiSelectOption );
