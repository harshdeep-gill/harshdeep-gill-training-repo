/**
 * ToastMessage Class.
 */
class ToastMessage extends HTMLElement {
	/**
	 * Properties.
	 */
	private closeButton: HTMLButtonElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.closeButton = this.querySelector( '.toast-dismiss' );

		// Events.
		this.closeButton?.addEventListener( 'click', this.hide.bind( this ) );
	}

	/**
	 * Hide the toast message.
	 */
	hide(): void {
		// Hide the toast.
		this.removeAttribute( 'visible' );
	}

	/**
	 * Show the toast message.
	 */
	show(): void {
		// Show the toast message.
		this.setAttribute( 'visible', 'true' );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-toast-message', ToastMessage );
