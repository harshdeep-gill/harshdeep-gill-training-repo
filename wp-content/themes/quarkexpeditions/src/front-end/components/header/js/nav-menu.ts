/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal Dependencies.
 */
import { slideElementDown, slideElementUp } from '../../../global/utility';

/**
 * HeaderNavMenu Class.
 */
export default class HeaderNavMenu extends HTMLElement {
	/**
	 * Properties.
	 */
	private subMenuArrows: NodeListOf<HTMLElement>;
	private hamburgerEl: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.subMenuArrows = this.querySelectorAll( '.menu-item-has-children > .sub-menu-arrow' );
		this.hamburgerEl = document.querySelector( '.header__hamburger' );
	}

	/**
	 * Contected callback.
	 */
	connectedCallback() {
		// Event for toggling child menus.
		this.subMenuArrows?.forEach( ( arrowEl: HTMLElement ): void => {
			// Add event.
			arrowEl.addEventListener( 'click', this.toggleChildMenus.bind( this ) );
		} );
	}

	/**
	 * Check if it's a desktop menu.
	 *
	 * @return {boolean} Is desktop menu.
	 */
	isDesktopMenu() {
		// Return true if hamburger element is not visible, which means it's a desktop menu.
		return this.hamburgerEl && 'none' === getComputedStyle( this.hamburgerEl ).display;
	}

	/**
	 * Handles click event of the sub-menu arrows.
	 *
	 * @param {Event} event Event object.
	 */
	toggleChildMenus( event: Event ) {
		// Get target element.
		const targetEl = event.target as HTMLElement;

		// If it's a desktop menu, return, because then we don't need the toggle menu option in desktop.
		if ( this.isDesktopMenu() ) {
			// Early return.
			return;
		}

		// Toggle child menus.
		const parentNode = targetEl.parentNode as HTMLElement;
		const subMenuEl = parentNode?.querySelector( '.sub-menu' ) as HTMLElement;

		// Check submenu.
		if ( ! subMenuEl ) {
			// Early return.
			return;
		}

		// Toggle attribute.
		parentNode.toggleAttribute( 'active' );

		// If parent node has active attribute, slide down.
		if ( parentNode.hasAttribute( 'active' ) ) {
			// Slide down.
			slideElementDown( subMenuEl, 600 );
		} else {
			// Slide up.
			slideElementUp( subMenuEl, 600 );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'tcs-header-nav-menu', HeaderNavMenu );
