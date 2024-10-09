/**
 * Global variables.
 */
const { HTMLElement } = window;

export class QuarkDialogCloseElement extends HTMLElement {
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
		this.addEventListener( 'click', this.closeDialog.bind( this ) );
	}

    /**
	 * Event: 'click'.
	 *
	 * Closes Dialog On Element Click.
	 */
	closeDialog() {
		// Declare dialog.
		const dialog = this.dialog;

		// Open the dialog.
		dialog?.close();

        // Remove scroll from body.
        document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );

		// Remove and add classes for slide out animation.
		dialog?.classList.add( 'dialog--close' );
		dialog?.addEventListener( 'animationend',
			() => {
                dialog?.classList.remove( 'dialog--close' )
            },
			{ once: true }
		);
	}
}