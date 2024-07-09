/**
 * Global variables.
 */
const { HTMLElement, customElements } = window;

/**
 * QuarkCountrySelectorElement Class.
 */
export default class QuarkCountrySelectorElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly countrySelector: HTMLElement | null;
	private readonly stateSelectors: NodeListOf<HTMLElement> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.countrySelector = this.querySelector( '.country-selector__country' );
		this.stateSelectors = this.querySelectorAll( '.country-selector__state' );

		// Events.
		if ( this.stateSelectors ) {
			this.countrySelector?.querySelector( 'select' )?.addEventListener( 'change', this.changeCountry.bind( this ) );
		}

		// Trigger change in country.
		this.changeCountry();
	}

	/**
	 * Event: Country changed.
	 */
	changeCountry(): void {
		// Check if we have states.
		if ( ! this.stateSelectors ) {
			// No states found, bail early.
			return;
		}

		// Get country.
		const country: string = this.countrySelector?.querySelector( 'select' )?.value ?? '';

		// Show / hide states based on country.
		this.stateSelectors.forEach( ( state: HTMLElement ): void => {
			// Check if state's country matches current country.
			if ( state.getAttribute( 'data-country' ) === country ) {
				state.setAttribute( 'data-visible', 'true' );
				state.querySelector( 'select' )?.setAttribute( 'name', state.getAttribute( 'data-name' ) ?? '' );
			} else {
				state.removeAttribute( 'data-visible' );
				state.querySelector( 'select' )?.removeAttribute( 'name' );
			}
		} );
	}
}

// Define the element.
customElements.define( 'quark-country-selector', QuarkCountrySelectorElement );
