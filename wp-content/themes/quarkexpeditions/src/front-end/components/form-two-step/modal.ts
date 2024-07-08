/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependency
 */
import { TPFormElement } from '@travelopia/web-components';

/**
 * FormTwoStepModal Class.
 */
export default class FormTwoStepModal extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly toastMessage: ToastMessage | null;
	private readonly tpForm: TPFormElement | null;
	private readonly quarkForm: HTMLElement | null;
	private readonly thankYou: HTMLElement | null;
	private readonly content: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.quarkForm = this.querySelector( 'quark-form' );
		this.tpForm = this.querySelector( 'tp-form' );
		this.toastMessage = this.querySelector( 'quark-toast-message' );
		this.thankYou = this.querySelector( '.form-two-step__thank-you' );
		this.content = this.querySelector( '.form-two-step__content' );

		// Events.
		this.tpForm?.addEventListener( 'validation-error', this.showToastMessage.bind( this ) );
		this.tpForm?.addEventListener( 'validation-success', this.hideToastMessage.bind( this ) );
		this.quarkForm?.addEventListener( 'api-success', this.showThankYouMessage.bind( this ) );
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

	/**
	 * Show thank you message.
	 */
	showThankYouMessage(): void {
		// Check if we have content and thank you.
		if ( ! this.content || ! this.thankYou ) {
			// We don't, bail!
			return;
		}

		// Hide content and show thank you instead.
		this.content.style.display = 'none';
		this.thankYou.style.display = 'flex';
	}
}
