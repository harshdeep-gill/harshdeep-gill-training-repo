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
	constructor() {
		// super
		super();

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

		// close the drawer
		drawer?.close();
	}
}
