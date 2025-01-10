/**
 * Global variables.
 */
const { HTMLElement, addEventListenerWithYieldToMain } = window;

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

		// Get the dialog element.
		this.dialog = this.closest( 'quark-dialog dialog' );

		// Validate.
		if ( ! this.dialog || ! this.button ) {
			// Dialog or button not found, bail.
			return;
		}

		// Attach event listener.
		addEventListenerWithYieldToMain( this.button, 'click', this.closeDialog.bind( this ) );
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
