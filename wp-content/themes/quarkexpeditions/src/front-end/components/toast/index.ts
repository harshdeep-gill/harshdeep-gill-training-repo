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
		this.dismissToastButton?.addEventListener( 'click', this.removeSelf.bind( this ) );
	}

	/**
	 * Removes self from the DOM
	 *
	 * @memberof QuarkToast
	 */
	removeSelf() {
		// Remove self from DOM.
		this.parentElement?.removeChild( this );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-toast', QuarkToast );
