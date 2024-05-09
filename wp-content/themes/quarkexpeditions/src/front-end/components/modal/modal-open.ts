/**
 * External Dependencies.
 */
import '@travelopia/web-components/dist/modal';
import { TPModalElement } from '@travelopia/web-components';

/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * QuarkModalOpenElement Class.
 */
export class QuarkModalOpenElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private modal: TPModalElement | null | undefined;
	private modalId: string | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.modalId = this.getAttribute( 'modal-id' );

		// Check if modal id is not available, return.
		if ( ! this.modalId ) {
			// Modal ID not found, bail early.
			return;
		}

		// Get the modal element.
		this.modal = document.getElementById( this.modalId ) as TPModalElement|null;

		// Check if modal is found.
		if ( ! this.modal ) {
			// Modal not found, bail.
			return;
		}

		// Events.
		this.addEventListener( 'click', this.openModal.bind( this ) );
		this.modal.addEventListener( 'close', this.handleModalClose.bind( this ) );
	}

	/**
	 * Event: 'click'.
	 *
	 * Opens Modal On Element Click.
	 */
	openModal() {
		// Declare modal.
		const modal = this.modal;

		// Open the modal.
		modal?.open();

		// Removes scroll from body.
		document.querySelector( 'body' )?.classList?.add( 'prevent-scroll' );

		// Add animation.
		modal?.classList.add( 'modal--open' );
		modal?.addEventListener( 'animationend',
			() => modal?.classList.remove( 'modal--open' ),
			{ once: true }
		);
	}

	/**
	 * Event: 'close'.
	 *
	 * Handles the close event of TPModalElement.
	 *
	 * @param { Event } evt
	 */
	handleModalClose( evt: Event ) {
		// Assign for easy use in the event handler.
		const modal = this.modal;

		// Check event target.
		if ( modal !== evt.target ) {
			// Modal is not the target, bail.
			return;
		}

		// Check if modal is there.
		if ( ! modal ) {
			//modal not found, bail.
			return;
		}

		// Reopen the modal.
		modal.setAttribute( 'open', 'yes' );

		// Remove and add classes for slide out animation.
		modal.classList.add( 'modal--close' );
		modal.addEventListener( 'animationend', function() {
			// Slide out.
			modal.classList.remove( 'modal--close' );
			modal.removeAttribute( 'open' );
			document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );
		}, { once: true } );
	}
}
