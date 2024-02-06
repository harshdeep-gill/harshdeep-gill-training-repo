/**
 * External Dependencies.
 */
import '@travelopia/web-components/dist/modal';
import { TPModalElement } from '@travelopia/web-components';

/**
 * TP Modal Close.
 */
export class QuarkCloseModalElement extends HTMLElement {
	/**
	 * Connected callback.
	 */
	connectedCallback(): void {
		// Events
		const button: HTMLButtonElement | null = this.querySelector( 'button' );
		button?.addEventListener( 'click', this.closeModal.bind( this ) );
	}

	/**
	 * Close the modal.
	 */
	closeModal(): void {
		// Handle closing.
		const modal: TPModalElement | null = this.closest( 'tp-modal' );

		// Remove and add classes for slide out animation.
		modal?.classList.add( 'modal--close' );
		modal?.addEventListener( 'animationend', function() {
			// Slide out.
			modal?.classList.remove( 'modal--close' );
			modal?.close();
		}, { once: true } );
	}
}
