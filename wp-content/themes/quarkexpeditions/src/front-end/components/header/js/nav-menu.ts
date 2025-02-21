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
	private bodyElement: HTMLElement | null;
	private menuButton: HTMLButtonElement | null;

	// Static property to ensure listeners are added only once.
	private static isEventListenerAdded = false;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.bodyElement = document.querySelector( 'body' );
		this.menuButton = this.querySelector( 'button.header__nav-item-link' );

		// Event for dropdown button.
		this.menuButton?.addEventListener( 'click', this.toggle.bind( this ) );

		// Add global event listeners once.
		if ( ! HeaderNavMenu.isEventListenerAdded ) {
			HeaderNavMenu.addGlobalEventListeners();
			HeaderNavMenu.isEventListenerAdded = true;
		}
	}

	/**
	 * Add global event listeners.
	 */
	private static addGlobalEventListeners() {
		// Keyboard event to close dropdowns.
		const handleDropdownCloseOnKeyDown = ( event: KeyboardEvent ) => {
			// Close all dropdowns on 'Escape' key press.
			if ( event.key === 'Escape' ) {
				event.preventDefault();
				HeaderNavMenu.closeAllDropdowns();
			}
		};

		// Click event to close dropdowns.
		const handleDropdownCloseOnDocumentClick = ( event: Event ) => {
			// Get the target element.
			const targetElement = event.target as HTMLElement;

			// Close all dropdowns if the click is outside dropdowns and buttons.
			if ( ! targetElement.closest( '.header__nav-item-link' ) && ! targetElement.closest( '.header__nav-item-dropdown-content' ) ) {
				HeaderNavMenu.closeAllDropdowns();
			}
		};

		// Attach event listeners.
		window.addEventListener( 'keydown', handleDropdownCloseOnKeyDown );
		window.addEventListener( 'click', handleDropdownCloseOnDocumentClick );
	}

	/**
	 * Toggle the dropdown.
	 */
	toggle() {
		// Check if the dropdown is open.
		if ( this.getAttribute( 'open' ) === 'true' ) {
			this.close();
		} else {
			this.open();
		}
	}

	/**
	 * Open dropdown.
	 */
	open() {
		// Close all dropdowns before opening this one.
		HeaderNavMenu.closeAllDropdowns();
		this.setAttribute( 'open', 'true' );
		this.bodyElement?.classList.add( 'has-navigation-dropdown-open' );
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'open' attribute.
		this.removeAttribute( 'open' );
		this.bodyElement?.classList.remove( 'has-navigation-dropdown-open' );
	}

	/**
	 * Close all dropdowns.
	 */
	private static closeAllDropdowns() {
		// Get all dropdowns.
		const dropdowns = document.querySelectorAll( 'quark-header-nav-menu-dropdown' );
		dropdowns.forEach( ( dropdown ) => dropdown.removeAttribute( 'open' ) );
		document.body.classList.remove( 'has-navigation-dropdown-open' );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-header-nav-menu-dropdown', HeaderNavMenu );
