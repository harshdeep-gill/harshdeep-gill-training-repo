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
	}

	/**
	 * Contected callback.
	 */
	connectedCallback() {
		// Event for Search Button.
		this.menuButton?.addEventListener( 'click', this.open.bind( this ) );

		// Event to close the search form on keydown.
		this.ownerDocument.defaultView?.addEventListener( 'keydown', this.handleDropdownCloseOnKeyDown.bind( this ) );

		// Event to close search form on document click.
		this.ownerDocument.defaultView?.addEventListener( 'click', this.handleDropdownCloseOnDocumentClick.bind( this ) );
	}

	/**
	 * Open dropdown.
	 */
	open() {
		// Toggle active attribute.
		this.toggleAttribute( 'active' );
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'active' attribute.
		this.removeAttribute( 'active' );
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

		// Close search form.
		event.preventDefault();
		this.close();
	}

	/**
	 * Handle Search Close,
	 * if we click on anywhere else on the
	 * HTML document.
	 *
	 * @param {Event} event Event.
	 */
	handleDropdownCloseOnDocumentClick( event: Event ) {
		// Get target element.
		const targetEl = event.target as HTMLElement;

		// If user has clicked inside search filter or search button cta, return.
		if ( targetEl.closest( '.header__nav-item' ) || targetEl.closest( '.header__nav-item-dropdown-content' ) ) {
			// Early return.
			return;
		}

		// Close the search.
		this.close();
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-header-nav-menu-dropdown', HeaderNavMenu );
