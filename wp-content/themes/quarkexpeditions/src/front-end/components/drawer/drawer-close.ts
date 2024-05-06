/**
 * Internal dependencies.
 */
import { QuarkDrawerElement } from './drawer';

/**
 * Drawer Close.
 */
export class QuarkDrawerCloseElement extends HTMLElement {
	/**
	 * Constructor.
	 */
	connectedCallback() {
		// Get the button inside.
		const button: HTMLButtonElement | null = this.querySelector( 'button' );

		// Events
		button?.addEventListener( 'click', this.closeDrawer.bind( this ) );
	}

	/**
	 * Close the drawer.
	 */
	closeDrawer(): void {
		// Get the drawer
		const drawer: QuarkDrawerElement | null = this.closest( 'quark-drawer' );

		// Check if drawer is there.
		if ( ! drawer ) {
			//drawer not found, bail.
			return;
		}

		// Remove and add classes for slide out animation.
		drawer.classList.add( 'drawer--close' );
		drawer.addEventListener( 'animationend', function() {
			// Slide out.
			drawer.classList.remove( 'drawer--close' );

			// Close the drawer
			drawer.close();
		}, { once: true } );
	}
}
