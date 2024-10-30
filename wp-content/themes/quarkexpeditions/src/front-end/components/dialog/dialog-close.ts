/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * QuarkDialogCloseElement Class.
 */
export class QuarkDialogCloseElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private dialog: HTMLDialogElement | null;
	private button: HTMLButtonElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Get the button inside.
		this.button = this.querySelector( 'button' );

		// Attach event listener.
		this.button?.addEventListener( 'click', this.closeDialog.bind( this ) );

		// Get the dialog element.
		this.dialog = this.closest( 'quark-dialog dialog' );
	}

	/**
	 * Event: 'click'.
	 *
	 * Closes Dialog On Element Click.
	 */
	closeDialog() {
		// Close dialog.
		this.dialog?.close();

		// Toggle open attribute.
		this.dialog?.parentElement?.toggleAttribute( 'open' );

		// Remove scroll from body.
		document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );
	}
}
