/**
 * Internal Dependencies.
 */
import { QuarkDrawerElement } from './drawer';

/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * QuarkDrawerOpenElement Class.
 */
export class QuarkDrawerOpenElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private drawer: QuarkDrawerElement | null | undefined;
	private drawerId: string | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.drawerId = this.getAttribute( 'drawer-id' );

		// Check if drawer id is not available, return.
		if ( ! this.drawerId ) {
			// Drawer ID not found, bail early.
			return;
		}

		// Get the drawer element.
		this.drawer = document.getElementById( this.drawerId ) as QuarkDrawerElement|null;

		// Check if drawer is found.
		if ( ! this.drawer ) {
			// Drawer not found, bail.
			return;
		}

		// Events.
		this.addEventListener( 'click', this.openDrawer.bind( this ) );
	}

	/**
	 * Event: 'click'.
	 *
	 * Opens Drawer On Element Click.
	 */
	openDrawer() {
		// Open the drawer.
		this.drawer?.open();
	}
}
