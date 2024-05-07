/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * HamburgerMenu Class.
 */
export default class HamburgerMenu extends HTMLElement {
	/**
	 * Properties.
	 */
	private body: HTMLBodyElement | null;
	private headerNav: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.body = document.querySelector( 'body' );
		this.headerNav = document.querySelector( '.header__nav' );
	}

	/**
	 * Contected callback.
	 */
	connectedCallback() {
		// Event.
		this.addEventListener( 'click', this.hamburgerClicked.bind( this ) );
	}

	/**
	 * Event: Button clicked.
	 */
	hamburgerClicked() {
		// Toggle active attribute and 'has-mobile-navigation-open' class.
		this.toggleAttribute( 'active' );
		this.body?.classList.toggle( 'has-mobile-navigation-open' );
		this.headerNav?.toggleAttribute( 'active' );
	}
}

/**
 * Initialize.
 */
customElements.define( 'tcs-header-hamburger', HamburgerMenu );
