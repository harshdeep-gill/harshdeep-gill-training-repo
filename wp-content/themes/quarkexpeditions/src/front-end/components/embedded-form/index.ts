/**
 * EmbeddedForm Class.
 */
class EmbeddedForm extends HTMLElement {
	/**
	 * Properties.
	 */
	private countriesSelector: HTMLSelectElement | null;
	private stateSelectorAustralia: HTMLSelectElement | null;
	private stateSelectorCanada: HTMLSelectElement | null;
	private stateSelectorUS: HTMLSelectElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent
		super();

		// Initialize the select elements
		this.countriesSelector = document.getElementById( 'country-selector' ) as HTMLSelectElement;
		this.stateSelectorAustralia = document.getElementById( 'state-selector-australia' ) as HTMLSelectElement;
		this.stateSelectorCanada = document.getElementById( 'state-selector-canada' ) as HTMLSelectElement;
		this.stateSelectorUS = document.getElementById( 'state-selector-us' ) as HTMLSelectElement;

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

		// Events
		this.countriesSelector.addEventListener( 'change', this.renderAppropriateStateSelector.bind( this ) );
	}

	/**
	 * Render appropriate state selector based on the selected country.
	 *
	 * @memberof EmbeddedForm
	 */
	renderAppropriateStateSelector() {
		// Hiding
		this.stateSelectorAustralia?.parentElement?.classList.add( 'hide' );
		this.stateSelectorCanada?.parentElement?.classList.add( 'hide' );
		this.stateSelectorUS?.parentElement?.classList.add( 'hide' );

		// Check the value.
		switch ( this.countriesSelector?.value ) {
			case 'AU':
				this.stateSelectorAustralia?.parentElement?.classList.remove( 'hide' );
				break;
			case 'CA':
				this.stateSelectorCanada?.parentElement?.classList.remove( 'hide' );
				break;
			case 'US':
				this.stateSelectorUS?.parentElement?.classList.remove( 'hide' );
				break;
		}
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-embedded-form', EmbeddedForm );
