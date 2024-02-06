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
 * QuarkOpenModalElement Class.
 */
export class QuarkOpenModalElement extends HTMLElement {
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
		this.modal = document.getElementById( this.modalId ) as TPModalElement;

		// Event.
		this.querySelector( 'button' )?.addEventListener( 'click', this.openModal.bind( this ) );
		this.modal?.addEventListener( 'click', this.handleModalClose.bind( this ) );
	}

	/**
	 * Event: 'click'.
	 *
	 * Open Modal On Element Click.
	 */
	openModal() {
		// Declare modal.
		const modal = this.modal;

		// Open the modal.
		modal?.open();
		modal?.classList.add( 'modal--open' );
		modal?.addEventListener( 'animationend',
			() => modal?.classList.remove( 'modal--open' ),
			{ once: true }
		);
	}

	/**
	 * Event: 'click'.
	 *
	 * Close Modal On Modal clicked or close modal clicked.
	 *
	 * @param {Event} evt
	 */
	handleModalClose( evt: Event ) {
		//Handle modal closing.
		if ( evt.target !== this.modal ) {
			// Modal not clicked, bail.
			return;
		}

		// Stop propagation.
		evt.stopPropagation();
		const modal = this.modal;

		// Check if modal is there.
		if ( ! modal ) {
			//modal not found, bail.
			return;
		}

		// Remove and add classes for slide out animation.
		modal?.classList.add( 'modal--close' );
		modal?.addEventListener( 'animationend', function() {
			// Slide out.
			modal?.classList.remove( 'modal--close' );
			modal?.close();
		}, { once: true } );
	}
}
