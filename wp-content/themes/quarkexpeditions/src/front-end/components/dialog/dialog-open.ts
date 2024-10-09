/**
 * Global variables.
 */
const { HTMLElement } = window;

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
		this.dialog = document.getElementById( this.dialogId ) as HTMLDialogElement|null;

		// Check if dialog is found.
		if ( ! this.dialog ) {
			// Dialog not found, bail.
			return;
		}

		// Events.
		this.addEventListener( 'click', this.openDialog.bind( this ) );
	}

	/**
	 * Event: 'click'.
	 *
	 * Opens Dialog On Element Click.
	 */
	openDialog() {
		// Declare dialog.
		const dialog = this.dialog;

		// Open the dialog.
		dialog?.showModal();

		// Add scroll from body.
		document.querySelector( 'body' )?.classList?.add( 'prevent-scroll' );

		// Add animation.
		dialog?.classList.add( 'dialog--open' );
		dialog?.addEventListener( 'animationend',
			() => {
				dialog?.classList.remove( 'dialog--open' )
			},
			{ once: true }
		);
	}
}
