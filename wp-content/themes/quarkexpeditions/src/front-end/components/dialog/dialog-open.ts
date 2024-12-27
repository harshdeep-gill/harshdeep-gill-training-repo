/**
 * Global variables.
 */
const { HTMLElement, addEventListenerWithYieldToMain } = window;

/**
 * QuarkDialogOpenElement Class.
 */
export class QuarkDialogOpenElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private dialog: HTMLDialogElement | null | undefined;
	private dialogId: string | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.dialogId = this.getAttribute( 'dialog-id' );

		// Check if dialog id is not available, return.
		if ( ! this.dialogId ) {
			// Dialog ID not found, bail early.
			return;
		}

		// Get the dialog element.
		this.dialog = document.querySelector( `#${ this.dialogId } dialog` ) as HTMLDialogElement|null;

		// Check if dialog is found.
		if ( ! this.dialog ) {
			// Dialog not found, bail.
			return;
		}

		// Events.
		addEventListenerWithYieldToMain( this, 'click', this.openDialog.bind( this ) );
	}

	/**
	 * Event: 'click'.
	 *
	 * Opens Dialog On Element Click.
	 */
	openDialog() {
		// Open the dialog.
		this.dialog?.showModal();

		// Toggle open attribute.
		this.dialog?.parentElement?.toggleAttribute( 'open' );

		// Add scroll from body.
		document.querySelector( 'body' )?.classList?.add( 'prevent-scroll' );
	}
}
