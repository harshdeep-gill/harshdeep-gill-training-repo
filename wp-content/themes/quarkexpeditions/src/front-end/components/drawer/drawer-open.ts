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
		this.drawerId = this.getAttribute( 'drawer_id' );

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
		// Declare drawer.
		const drawer = this.drawer;

		// Open the drawer.
		drawer?.open();

		// Removes scroll from body.
		document.querySelector( 'body' )?.classList?.add( 'prevent-scroll' );

		// Add animation.
		drawer?.classList.add( 'drawer--open' );
		drawer?.addEventListener( 'animationend',
			() => drawer?.classList.remove( 'drawer--open' ),
			{ once: true }
		);
	}
}
