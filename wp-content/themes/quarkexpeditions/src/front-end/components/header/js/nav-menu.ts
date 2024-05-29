/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * HeaderNavMenu Class.
 */
export default class HeaderNavMenu extends HTMLElement {
	/**
	 * Properties.
	 */
	private menuButton: HTMLButtonElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.menuButton = this.querySelector( 'button.header__nav-item-link' );

		// Event for dropdown button.
		this.menuButton?.addEventListener( 'click', this.toggle.bind( this ) );

		// Event to close the dropdown on keydown.
		this.ownerDocument.defaultView?.addEventListener( 'keydown', this.handleDropdownCloseOnKeyDown.bind( this ) );

		// Event to close dropdown on document click.
		this.ownerDocument.defaultView?.addEventListener( 'click', this.handleDropdownCloseOnDocumentClick.bind( this ) );
	}

	/**
	 * Toogle the dropdown.
	 */
	toggle() {
		// Check if the tooltip is open.
		if ( 'true' === this.getAttribute( 'open' ) ) {
			// Close, if open.
			this.close();
		} else {
			// Open, if closed.
			this.open();
		}
	}

	/**
	 * Open dropdown.
	 */
	open() {
		// Close all dropdowns.
		this.closeAllDropdowns();

		// Toggle `open` attribute.
		this.setAttribute( 'open', 'true' );
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'open' attribute.
		this.removeAttribute( 'open' );
	}

	/**
	 * Close the all dropdowns.
	 */
	closeAllDropdowns() {
		// Get all dropdowns.
		const dropdowns = document.querySelectorAll( 'quark-header-nav-menu-dropdown' );

		// Close all opened dropdowns.
		dropdowns.forEach( ( dropdown ) => {
			// Remove 'open' attribute.
			dropdown.removeAttribute( 'open' );
		} );
	}

	/**
	 * Event: 'keydown'
	 *
	 * @param {KeyboardEvent} event Event.
	 */
	handleDropdownCloseOnKeyDown( event: KeyboardEvent ) {
		// If the escape key is pressed, return.
		if ( 'Escape' !== event.key ) {
			// Early return.
			return;
		}

		// Close dropdown.
		event.preventDefault();
		this.close();
	}

	/**
	 * Handle Dropdown Close,
	 * if we click on anywhere else on the
	 * HTML document.
	 *
	 * @param {Event} event Event.
	 */
	handleDropdownCloseOnDocumentClick( event: Event ) {
		// Get target element.
		const targetElement = event.target as HTMLElement;

		// If user has clicked inside dropdown or dropdown button cta, return.
		if ( targetElement.closest( '.header__nav-item-link' ) || targetElement.closest( '.header__nav-item-dropdown-content' ) ) {
			// Early return.
			return;
		}

		// Close the dropdown.
		this.close();
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-header-nav-menu-dropdown', HeaderNavMenu );
