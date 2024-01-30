/**
 * External dependency
 */
import { TPFormFieldElement } from '@travelopia/web-components';

/**
 * InquiryForm Class.
 */
class InquiryForm extends HTMLElement {
	/**
	 * Properties.
	 */
	private countriesSelector: HTMLSelectElement | null;
	private stateSelectorAustralia: TPFormFieldElement | null;
	private stateSelectorCanada: TPFormFieldElement | null;
	private stateSelectorUS: TPFormFieldElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent
		super();

		// Initialize the select elements
		this.countriesSelector = this.querySelector( '.inquiry-form-modal-country' ) as HTMLSelectElement;
		this.stateSelectorAustralia = this.querySelector( 'tp-form-field[data-country="AU"]' ) as TPFormFieldElement;
		this.stateSelectorCanada = this.querySelector( 'tp-form-field[data-country="CA"]' ) as TPFormFieldElement;
		this.stateSelectorUS = this.querySelector( 'tp-form-field[data-country="US"]' ) as TPFormFieldElement;

		// No point in setting up if the fields are not there.
		if ( ! (
			this.countriesSelector &&
			this.stateSelectorAustralia &&
			this.stateSelectorCanada &&
			this.stateSelectorUS
		) ) {
			// Bail early.
			return;
		}

		// Initial render
		this.renderAppropriateStateSelector();

		// Events
		this.countriesSelector.addEventListener( 'change', this.renderAppropriateStateSelector.bind( this ) );
	}

	/**
	 * Render appropriate state selector based on the selected country.
	 *
	 * @memberof InquiryForm
	 */
	renderAppropriateStateSelector() {
		// Hiding
		this.hideStateSelector( this.stateSelectorAustralia );
		this.hideStateSelector( this.stateSelectorCanada );
		this.hideStateSelector( this.stateSelectorUS );

		// Check the value.
		switch ( this.countriesSelector?.value ) {
			case 'AU':
				this.showStateSelector( this.stateSelectorAustralia );
				break;
			case 'CA':
				this.showStateSelector( this.stateSelectorCanada );
				break;
			case 'US':
				this.showStateSelector( this.stateSelectorUS );
				break;
		}
	}

	/**
	 * Hide field.
	 *
	 * @param {TPFormFieldElement} selector
	 * @memberof InquiryForm
	 */
	hideStateSelector( selector: TPFormFieldElement | null ) {
		// Hide selector
		selector?.setAttribute( 'data-hide', '' );
		selector?.removeAttribute( 'required' );
	}

	/**
	 * Show field.
	 *
	 * @param {(TPFormFieldElement | null)} selector
	 * @memberof InquiryForm
	 */
	showStateSelector( selector: TPFormFieldElement | null ) {
		// Show selector
		selector?.removeAttribute( 'data-hide' );
		selector?.setAttribute( 'required', 'yes' );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-inquiry-form', InquiryForm );
