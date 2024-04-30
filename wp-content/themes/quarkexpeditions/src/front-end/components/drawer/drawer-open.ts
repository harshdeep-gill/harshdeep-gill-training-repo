/**
 * External Dependencies.
 */
import { TPModalElement } from '@travelopia/web-components';

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
	private drawer: TPModalElement | null | undefined;
	private drawerId: string | null;
	private drawerResizeObserver: ResizeObserver | undefined;
	private drawerContentElement: HTMLElement | null | undefined;
	private drawerBodyElement: HTMLElement | null | undefined;

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
		this.drawer = document.getElementById( this.drawerId ) as TPModalElement|null;

		// Check if drawer is found.
		if ( ! this.drawer ) {
			// Drawer not found, bail.
			return;
		}

		// Events.
		this.addEventListener( 'click', this.openDrawer.bind( this ) );
		this.drawer.addEventListener( 'close', this.handleDrawerClose.bind( this ) );

		// Initialize remaining properties.
		this.drawerContentElement = this.drawer.querySelector<HTMLElement>( '.drawer__content' );
		this.drawerBodyElement = this.drawerContentElement?.querySelector( '.drawer__body' );

		// Check if drawerContent found
		if ( this.drawerContentElement && this.drawerBodyElement ) {
			/**
			 * We are observing the drawer instead of the drawerContentElement
			 * because the max height of drawerContentElement depends on the
			 * height of the viewport and drawer is supposed to take up the whole
			 * viewport.
			 */
			this.drawerResizeObserver = new ResizeObserver( this.observeDrawerHeight.bind( this ) );
			this.drawerResizeObserver.observe( this.drawer );
		}
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

	/**
	 * Event: 'close'.
	 *
	 * Handles the close event of TPModalElement.
	 */
	handleDrawerClose() {
		// Assign for easy use in the event handler.
		const drawer = this.drawer;

		// Check if drawer is there.
		if ( ! drawer ) {
			//drawer not found, bail.
			return;
		}

		// Reopen the drawer.
		drawer.setAttribute( 'open', 'yes' );

		// Remove and add classes for slide out animation.
		drawer.classList.add( 'drawer--close' );
		drawer.addEventListener( 'animationend', function() {
			// Slide out.
			drawer.classList.remove( 'drawer--close' );
			drawer.removeAttribute( 'open' );
			document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );
		}, { once: true } );
	}

	/**
	 * Observes and resizes the body according to the size of its sibling
	 * elements and the containing drawer__content.
	 *
	 * @param { ResizeObserverEntry[] } entries
	 */
	observeDrawerHeight( entries: ResizeObserverEntry[]|null[] ): void {
		// Loop over entries.
		entries.forEach( this.resizeDrawerBody.bind( this ) );
	}

	/**
	 * Resizes the drawer body dynamically.
	 */
	resizeDrawerBody(): void {
		// Check if we have content and element.
		if ( ! this.drawerContentElement || ! this.drawerBodyElement ) {
			// We need both for this.
			return;
		}

		// Create references.
		const drawerContentElement = this.drawerContentElement as HTMLElement;
		const drawerBodyElement = this.drawerBodyElement as HTMLElement;

		// Get drawer content max height.
		let drawerContentMaxHeight = parseInt( getComputedStyle( drawerContentElement ).maxHeight );
		drawerContentMaxHeight = ! Number.isNaN( drawerContentMaxHeight ) ? drawerContentMaxHeight : 0;

		// Calculate notBodyHeight
		const drawerContentRect = drawerContentElement.getBoundingClientRect();
		const drawerBodyRect = drawerBodyElement.getBoundingClientRect();
		let notBodyHeight = drawerContentElement.scrollHeight - drawerBodyRect.height;

		/**
		 * When we increase the viewport height, the maxHeight and current Height
		 * always have a difference of 1px when that resize happens. We want to
		 * re-introduce that when the viewport height is decreased to avoid discrepancies.
		 */
		if ( drawerContentMaxHeight === drawerContentRect.height ) {
			notBodyHeight++;
		}

		// New max height candidate for drawer__body
		const drawerBodyMaxHeightCandidate = drawerContentMaxHeight - notBodyHeight;

		// Set the drawer body element height.
		drawerBodyElement.style.maxHeight = `${ Math.min( drawerBodyMaxHeightCandidate, drawerBodyElement.scrollHeight ) }px`;
	}
}
