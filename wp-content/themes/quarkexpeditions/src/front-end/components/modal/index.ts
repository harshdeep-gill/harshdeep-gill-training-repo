/**
 * External Dependencies.
 */
import '@travelopia/web-components/dist/modal';
import { TPModalElement } from '@travelopia/web-components';

/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * OpenModalCta Class.
 */
class OpenModalCta extends HTMLElement {
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
		if ( this.parentElement ) {
			this.parentElement.addEventListener( 'click', () => this.openModal() );
		}
	}

	/**
	 * Event: 'click'.
	 *
	 * Open Modal On Element Click.
	 */
	openModal() {
		// Open the modal.
		this.modal?.open();
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-open-modal', OpenModalCta );
