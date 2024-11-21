/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * FormNewsletter Class.
 */
export default class FormNewsletter extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly quarkForm: HTMLElement | null;
	private readonly successMessage: HTMLElement | null;
	private readonly content: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.quarkForm = this.querySelector( 'quark-form' );
		this.successMessage = this.querySelector( '.form-newsletter__success' );
		this.content = this.querySelector( '.form-newsletter__content' );

		// Events
		this.quarkForm?.addEventListener( 'api-success', this.showSuccessMessage.bind( this ) );
	}

	/**
	 * Show thank you message.
	 */
	showSuccessMessage(): void {
		// Check if we have content and thank you.
		if ( ! this.content || ! this.successMessage ) {
			// We don't, bail!
			return;
		}

		// Hide content and show thank you instead.
		this.content.style.display = 'none';
		this.successMessage.style.display = 'flex';

		// Scroll to the success message
		this.scrollToSuccessMessage();
	}

	/**
	 * Scroll to the success message element.
	 */
	scrollToSuccessMessage(): void {
		// Set the scroll element.
		const successElement = document.getElementById( 'form-newsletter__success' );

		// Check if the element exists.
		if ( successElement ) {
			// Scroll to the element.
			window.scrollTo( {
				top: 0,
				behavior: 'smooth',
			} );
		}
	}
}

// Define element.
customElements.define( 'quark-form-newsletter', FormNewsletter );
