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
	private readonly countryField: HTMLInputElement | null;
	private readonly stateField: HTMLInputElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.countrySelector = this.querySelector( '.country-selector__country' );
		this.stateSelectors = this.querySelectorAll( '.country-selector__state' );
		this.countryField = this.querySelector( '.country-selector__country-name' );
		this.stateField = this.querySelector( '.country-selector__state-name' );

		// Events.
		if ( this.stateSelectors ) {
			// Add event listeners for country.
			this.countrySelector?.querySelector( 'select' )?.addEventListener( 'change', this.changeCountry.bind( this ) );

			// Add event listeners for state.
			this.stateSelectors?.forEach( ( state: HTMLElement ): void => {
				// Add event listener for each state.
				state.querySelector( 'select' )?.addEventListener( 'change', this.changeState.bind( this ) );
			} );
		}

		// Trigger change in country.
		this.changeCountry();

		// Trigger change in State.
		this.changeState();
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

		// Update country field.
		if ( this.countryField ) {
			this.countryField.value = this.countrySelector?.querySelector( 'tp-multi-select-status' )?.innerHTML ?? '';
		}

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

	/**
	 * Event: State changed.
	 */
	changeState(): void {
		// Check if we have states.
		if ( ! this.stateSelectors ) {
			// No states found, bail early.
			return;
		}

		// Get country.
		const country: string = this.countrySelector?.querySelector( 'select' )?.value ?? '';

		// Get state.
		this.stateSelectors.forEach( ( state: HTMLElement ): void => {
			// Check if state's country matches current country.
			if ( state.getAttribute( 'data-country' ) === country ) {
				// Update state field.
				if ( this.stateField ) {
					this.stateField.value = state.querySelector( 'tp-multi-select-status' )?.innerHTML ?? '';
				}
			}
		} );
	}
}

// Define the element.
customElements.define( 'quark-country-selector', QuarkCountrySelectorElement );
