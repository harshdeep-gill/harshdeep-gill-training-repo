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
		this.stateSelectorAustralia?.setAttribute( 'data-hide', '' );
		this.stateSelectorCanada?.setAttribute( 'data-hide', '' );
		this.stateSelectorUS?.setAttribute( 'data-hide', '' );

		// Check the value.
		switch ( this.countriesSelector?.value ) {
			case 'AU':
				this.stateSelectorAustralia?.removeAttribute( 'data-hide' );
				break;
			case 'CA':
				this.stateSelectorCanada?.removeAttribute( 'data-hide' );
				break;
			case 'US':
				this.stateSelectorUS?.removeAttribute( 'data-hide' );
				break;
		}
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-inquiry-form', InquiryForm );
