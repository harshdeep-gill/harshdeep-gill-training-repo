/**
 * Internal dependencies.
 */
import { QuarkDrawerElement } from './drawer';

/**
 * Drawer Close.
 */
export class QuarkDrawerCloseElement extends HTMLElement {
	/**
	 * Properties
	 */
	private drawer: QuarkDrawerElement | null;
	private button: HTMLButtonElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// super
		super();

		// Get the button inside.
		this.button = this.querySelector( 'button' );

		// Events
		this.button?.addEventListener( 'click', this.closeDrawer.bind( this ) );

		// Get the drawer
		this.drawer = this.closest( 'quark-drawer' );
	}

	/**
	 * Close the drawer.
	 */
	closeDrawer(): void {
		// close the drawer
		this.drawer?.close();
	}
}
