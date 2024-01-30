/**
 * QuarkToast Class.
 */
class QuarkToast extends HTMLElement {
	/**
	 * Properties.
	 */
	private dismissToastButton: HTMLButtonElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Select the dismiss button.
		this.dismissToastButton = this.querySelector( '.toast-dismiss' );

		// Event
		this.dismissToastButton?.addEventListener( 'click', this.hide.bind( this ) );
	}

	/**
	 * Hides the toast
	 *
	 * @memberof QuarkToast
	 */
	hide() {
		// Hide the toast.
		this.classList.add( 'toast--hidden' );
	}

	/**
	 * Shows the toast.
	 *
	 * @memberof QuarkToast
	 */
	show() {
		// Show the toast
		this.classList.remove( 'toast--hidden' );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-toast', QuarkToast );
