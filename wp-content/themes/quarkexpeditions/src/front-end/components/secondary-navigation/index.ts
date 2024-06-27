/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Sub Navigation Class.
 */
export default class SecondaryNavigation extends HTMLElement {
	/**
	 * Properties.
	 */
	private navContainer: HTMLElement | null;
	private navItems: HTMLElement | null;
	private moreMenu: HTMLElement | null;
	private moreDropdown: HTMLElement | null;
	private dropdownButton: HTMLElement | null;
	private allContentItems: NodeListOf<Element> | null;
	private levelTwoHeadings: NodeListOf<Element> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.navContainer = this.querySelector( '.secondary-navigation__navigation' );
		this.navItems = this.querySelector( '.secondary-navigation__navigation-items' );
		this.moreMenu = this.querySelector( '.secondary-navigation__navigation-item--dropdown' );
		this.moreDropdown = this.querySelector( '.secondary-navigation__navigation-dropdown' );
		this.dropdownButton = this.querySelector( '.secondary-navigation__navigation-button' );
		this.allContentItems = this.querySelectorAll( '.secondary-navigation__navigation-item' );
		this.levelTwoHeadings = document.querySelectorAll( '.main h2' );
	}

	/**
	 * Connected callback to initialize the responsive navigation.
	 */
	connectedCallback(): void {
		// Initialize function.
		this.init();
	}

	/**
	 * Initialize the responsive navigation by adding event listeners
	 * and performing the initial layout update.
	 */
	private init(): void {
		// Run on resize events.
		window.addEventListener( 'resize', this.updateNav.bind( this ) );
		this.updateNav();

		// Highlight content item by hash on initial render.
		this.highlightContentItemByHash();

		// Initialize IntersectionObserver to highlight item on scroll.
		this.highlightItemOnScroll();

		// Event for dropdown button.
		this.dropdownButton?.addEventListener( 'click', this.toggle.bind( this ) );

		// Event to close the dropdown on keydown.
		this.ownerDocument.defaultView?.addEventListener( 'keydown', this.handleDropdownCloseOnKeyDown.bind( this ) );

		// Event to close dropdown on document click.
		this.ownerDocument.defaultView?.addEventListener( 'click', this.handleDropdownCloseOnDocumentClick.bind( this ) );
	}

	/**
	 * Check if it's mobile view.
	 */
	isMobile() {
		// Return true if screen is mobile.
		return 768 >= window.innerWidth;
	}

	/**
	 * Toogle the dropdown.
	 */
	toggle() {
		// Check if the dropdown is open.
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
		// Toggle `open` attribute.
		this.setAttribute( 'open', 'true' );
		this.moreMenu?.classList.add( 'secondary-navigation__navigation-item--dropdown-active' );
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'open' attribute.
		this.removeAttribute( 'open' );
		this.moreMenu?.classList.remove( 'secondary-navigation__navigation-item--dropdown-active' );
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
		if ( targetElement.closest( '.secondary-navigation__navigation-item--dropdown' ) ) {
			// Early return.
			return;
		}

		// Close the dropdown.
		this.close();
	}

	/**
	 * Update the navigation layout based on the container's width.
	 * Moves overflowing items to the "More" dropdown.
	 */
	private updateNav(): void {
		// Check for the screen size.
		if ( this.isMobile() ) {
			// Reset the dropdown.
			this.resetDropdown();

			// No, bail early.
			return;
		}

		// Return if any elements does not exist.
		if ( ! this.navContainer || ! this.moreMenu || ! this.navItems || ! this.moreDropdown ) {
			// No, bail early.
			return;
		}

		// Set container width and more menu width.
		const containerWidth = this.navContainer.clientWidth;
		const moreMenuWidth = this.moreMenu.offsetWidth;
		let totalWidth = moreMenuWidth;

		// Reset the more dropdown by moving items back to the main list
		this.resetDropdown();

		// Calculate widths and move items if necessary
		const navItemElements = Array.from( this.navItems.getElementsByClassName( 'secondary-navigation__navigation-item' ) );

		// For loop.
		for ( let i = 0; i < navItemElements.length - 1; i++ ) {
			// Set the item.
			const item = navItemElements[ i ] as HTMLElement;
			totalWidth += item.offsetWidth;

			// Check for widths.
			if ( totalWidth > containerWidth ) {
				// Append the item.
				this.moreDropdown.appendChild( item );
				totalWidth -= item.offsetWidth;
			}
		}

		// Hide or show the "More" menu based on whether there are overflow items.
		this.toggleMoreMenu();
	}

	/**
	 * Reset the "More" dropdown by moving all
	 * its items back to the main navigation list.
	 */
	private resetDropdown(): void {
		// Check for elements.
		if ( ! this.moreDropdown || ! this.navItems || ! this.moreMenu ) {
			// No, bail early.
			return;
		}

		// Check for first child.
		while ( this.moreDropdown.firstChild ) {
			// Insert the item.
			this.navItems.insertBefore( this.moreDropdown.firstChild, this.moreMenu );
		}
	}

	/**
	 * Toggle the visibility of the "More" menu based on the presence of overflow items.
	 */
	private toggleMoreMenu(): void {
		// Check if elements exist.
		if ( ! this.moreDropdown || ! this.moreMenu ) {
			// No, bail early.
			return;
		}

		// Check for dropdown children.
		if ( this.moreDropdown.children.length > 0 ) {
			// Toggle the classes.
			this.moreMenu.classList.add( 'secondary-navigation__navigation-item--dropdown-show' );
			this.moreMenu.classList.remove( 'secondary-navigation__navigation-item--dropdown-hide' );
		} else {
			// Toggle the classes
			this.moreMenu.classList.add( 'secondary-navigation__navigation-item--dropdown-hide' );
			this.moreMenu.classList.remove( 'secondary-navigation__navigation-item--dropdown-show' );
		}
	}

	/**
	 * Highlight content item by hash in url
	 */
	highlightContentItemByHash(): void {
		// Get the hash.
		const hash = window.location.hash;

		// Check if hash is not available, return.
		if ( ! hash ) {
			// Hash not found, bail early.
			return;
		}

		// Get the content item.
		const contentItem = this.querySelector( `.secondary-navigation__navigation-item[data-anchor="${ hash }"]` );

		// Check if content item is not available, return.
		if ( contentItem ) {
			// Set content item as active.
			this.setItemAsActive( contentItem );
		}
	}

	/**
	 * Set the passed item as active.
	 *
	 * @param {HTMLElement | Element} itemElement Content Item to be set as active.
	 */
	setItemAsActive( itemElement: HTMLElement | Element ) {
		// Check if item exists.
		if ( ! itemElement ) {
			// Bail early.
			return;
		}

		// Check if the passed element has the expected class.
		if ( ! itemElement.classList.contains( 'secondary-navigation__navigation-item' ) ) {
			// If not exists, bail early.
			return;
		}

		// Check for all content items.
		if ( this.allContentItems ) {
			// Loop through all content items.
			this.allContentItems.forEach( ( item ) => {
				// Remove any existing active class from other items.
				item.classList.remove( 'secondary-navigation__navigation-item--active' );
			} );
		}

		// Add class to the current active content item.
		itemElement.classList.add( 'secondary-navigation__navigation-item--active' );
	}

	/**
	 * Highlight content item on scroll.
	 */
	highlightItemOnScroll() {
		// Get the header height.
		const headerHeight = getComputedStyle( document.body )?.getPropertyValue( '--header-height' ) ?? 0;

		// Calculate the top root margin.
		const rootMarginTop = ( parseInt( headerHeight ) + 24 ) + 'px';

		// Instantiate IntersectionObserver.
		const observer = new IntersectionObserver(
			this.intersectionObserverCallback.bind( this ),
			{
				rootMargin: `${ rootMarginTop } 0px -75% 0px`,
				threshold: 1,
			} );

		// Loop thorugh all headings.
		if ( this.levelTwoHeadings ) {
			this.levelTwoHeadings?.forEach( ( heading ) => {
				// Observe each heading.
				observer.observe( heading );
			} );
		}
	}

	/**
	 * Intersection Observer Callback.
	 *
	 * @param {IntersectionObserverEntry[]} entries Observed Entries.
	 */
	intersectionObserverCallback( entries: IntersectionObserverEntry[] ) {
		// Loop thorugh entries.
		entries.forEach( ( entry ) => {
			// Get value for isIntersecting.
			const isActive = entry.isIntersecting;

			// Get the heading id from current entry.
			const headingId = entry.target.id;

			// Get the content item.
			if ( headingId ) {
				const contentItem = this.querySelector( `.secondary-navigation__navigation-item[data-anchor="#${ headingId }"]` );

				// If no content item exists.
				if ( ! contentItem ) {
					// Bail early.
					return;
				}

				// Check if content item is not available, return.
				if ( isActive ) {
					// Set content item as active.
					this.setItemAsActive( contentItem );
				}
			}
		} );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-secondary-navigation', SecondaryNavigation );
