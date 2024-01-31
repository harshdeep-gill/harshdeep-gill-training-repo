/**
 * External dependency
 */
import { TPFormElement } from '@travelopia/web-components';

/**
 * InquiryForm Class.
 */
class InquiryForm extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly countrySelector: HTMLElement | null;
	private readonly stateSelectors: NodeListOf<HTMLElement> | null;
	private readonly toastMessage: ToastMessage | null;
	private readonly tpForm: TPFormElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tpForm = this.querySelector( 'tp-form' ) as TPFormElement;
		this.countrySelector = this.querySelector( '.inquiry-form__country' );
		this.stateSelectors = this.querySelectorAll( '.inquiry-form__state' );
		this.toastMessage = this.querySelector( 'quark-toast-message' );

		// Events.
		if ( this.stateSelectors ) {
			this.countrySelector?.querySelector( 'select' )?.addEventListener( 'change', this.changeCountry.bind( this ) );
		}
		this.tpForm?.addEventListener( 'validation-error', this.showToastMessage.bind( this ) );
		this.tpForm?.addEventListener( 'validation-success', this.hideToastMessage.bind( this ) );

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

	/**
	 * Show toast message.
	 */
	showToastMessage(): void {
		// Show toast message.
		this.toastMessage?.show();
	}

	/**
	 * Hide toast message.
	 */
	hideToastMessage(): void {
		// Hide toast message.
		this.toastMessage?.hide();
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-inquiry-form', InquiryForm );
