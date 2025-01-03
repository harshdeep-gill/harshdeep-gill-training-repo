/**
 * Internal dependencies.
 */
import { throttle } from '../../global/utility';

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
	private navigationContainer: HTMLElement | null;
	private navigationWrapper: HTMLElement | null;
	private navigationItems: HTMLElement | null;
	private moreMenu: HTMLElement | null;
	private moreDropdown: HTMLElement | null;
	private dropdownButton: HTMLElement | null;
	private navigationElement: HTMLElement | null;
	private allContentItems: NodeListOf<Element> | null;
	private anchorItems: NodeListOf<HTMLElement>;
	private sections: Array<HTMLElement | null>;
	private mobileTopOffset: number;
	private desktopTopOffset: number;
	private headerHeight: number;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.navigationContainer = document.querySelector( '.secondary-navigation__navigation' );
		this.navigationWrapper = document.querySelector( '.secondary-navigation__wrap' );
		this.navigationItems = this.querySelector( '.secondary-navigation__navigation-items' );
		this.moreMenu = this.querySelector( '.secondary-navigation__navigation-item--dropdown' );
		this.moreDropdown = this.querySelector( '.secondary-navigation__navigation-dropdown' );
		this.dropdownButton = this.querySelector( '.secondary-navigation__navigation-button' );
		this.allContentItems = this.querySelectorAll( '.secondary-navigation__navigation-item' );
		this.anchorItems = this.querySelectorAll( '.secondary-navigation__navigation-item-link[href*="#"]' );
		this.sections = this.getMenuSections();
		this.navigationElement = document.querySelector( '.secondary-navigation' );
		this.headerHeight = parseInt( getComputedStyle( document.body ).getPropertyValue( '--header-height' ) );
		this.mobileTopOffset = this.headerHeight;
		this.desktopTopOffset = this.headerHeight;

		/**
		 * Event on 'scroll'.
		 *
		 * Reason for adding 'passive: true'
		 * When you attach an event listener to a scroll event,
		 * the browser has to wait for the JavaScript code to execute before it can continue scrolling.
		 * By marking the event listener as 'passive',
		 * you're telling the browser that the event handler will not prevent the default behavior of the event (like scrolling),
		 * allowing the browser to optimize the scroll performance.
		 * Without this you will get warning.
		 */
		this.ownerDocument.addEventListener( 'scroll', throttle( this.onScroll.bind( this ) ), { passive: true } );

		// Run on resize events.
		window.addEventListener( 'resize', throttle( this.updateNav.bind( this ) ) );
		this.updateNav();

		// Highlight content item by hash on initial render.
		this.highlightContentItemByHash();

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
		return 1024 > window.innerWidth;
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
	 * Toggle the class on scroll event.
	 */
	onScroll() {
		// Check for element.
		if ( ! this.navigationElement ) {
			// No, bail early.
			return;
		}

		// Set top spacing
		let topSpacing = 12;

		// Check for the class.
		if ( document.body.classList.contains( 'admin-bar' ) ) {
			// Set the topSpacing.
			topSpacing = document.getElementById( 'wpadminbar' )?.offsetHeight ?? topSpacing;
			topSpacing = topSpacing + 12;
		}

		// Add header height to top spacing with padding.
		topSpacing += this.headerHeight - 32;

		// Check if the page is scrolled down.
		if ( this.navigationElement?.getBoundingClientRect()?.top < topSpacing ) {
			// Add classes.
			this.navigationElement.classList.add( 'secondary-navigation--is-sticky' );
			document.body.classList.add( 'has-sticky-secondary-navigation' );
		} else {
			// Remove classes.
			this.navigationElement.classList.remove( 'secondary-navigation--is-sticky' );
			document.body.classList.remove( 'has-sticky-secondary-navigation' );
		}

		// Highlight the active navigation item.
		this.navigationHighlighter();
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
	 * Get Menu Sections.
	 * Returns all sections with an ID that contains menu items href.
	 *
	 * @return {Array<HTMLElement | null>} Array of sections.
	 */
	getMenuSections(): Array<HTMLElement | null> {
		// Return the array of sections.
		return Array.from( this.anchorItems ).map( ( anchorItem: HTMLElement ) => {
			// Get the ID.
			let id = anchorItem.getAttribute( 'href' ) || '';
			id = id.replace( '#', '' );

			// If id not present return.
			if ( ! id ) {
				// Return.
				return null;
			}

			// Return the element.
			return document.querySelector( `#${ id }` );
		} );
	}

	/**
	 * Handle Navigation Highlights.
	 */
	navigationHighlighter() {
		// Check if sections does not exist.
		if ( ! this.sections || ! this.sections.length ) {
			// No, bail early.
			return;
		}

		// Get current scroll position
		const scrollY = window.scrollY;

		// Now we loop through sections to get height, top and ID values for each
		this.sections?.forEach( ( currentSection ) => {
			// Check if current section exists.
			if ( ! currentSection ) {
				// No, bail early.
				return;
			}

			// Initialize container section.
			let containerSection = currentSection;

			// Check if current section is a child of another section.
			if ( currentSection?.parentElement?.classList?.contains( 'section' ) ) {
				// Yes, get the parent element.
				containerSection = currentSection?.parentElement;
			}

			// Get values for current section.
			const sectionHeight = containerSection.offsetHeight;

			/**
			 * If it's mobile view, subtract the mobileTopOffset from container top offset and
			 * If it's desktop view, subtract the desktopTopOffset from container top offset and
			 */
			const sectionTop = this.isMobile() ? containerSection.offsetTop - this.mobileTopOffset : containerSection.offsetTop - this.desktopTopOffset;
			const sectionId = currentSection.getAttribute( 'id' );
			const activeItem = document.querySelector( '.secondary-navigation__navigation-item[data-anchor="#' + sectionId + '"]' );

			/**
			 * If our current scroll position enters the space where current section on screen is,
			 * add .active class to corresponding navigation link, else remove it
			 * To know which link needs an active class,
			 * we use sectionId variable we are getting while looping through sections as an selector.
			 */
			if (
				scrollY > sectionTop - ( window.innerHeight / 2 ) &&
				scrollY <= sectionTop + sectionHeight
			) {
				// Ensure only one item is active at a time.
				this.clearActiveItems();

				// Add active class.
				activeItem?.classList?.add( 'secondary-navigation__navigation-item--active' );

				// Scroll the active item to the center of the screen.
				if ( activeItem ) {
					// Scroll the active item.
					this.scrollLeftOnMobile( activeItem );
				}
			} else {
				activeItem?.classList?.remove( 'secondary-navigation__navigation-item--active' );
			}
		} );
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
		if ( ! this.navigationContainer || ! this.moreMenu || ! this.navigationItems || ! this.moreDropdown ) {
			// No, bail early.
			return;
		}

		// Set container width and more menu width.
		const containerWidth = this.navigationContainer.clientWidth;
		const moreMenuWidth = this.moreMenu.offsetWidth;
		let totalWidth = moreMenuWidth;

		// Reset the more dropdown by moving items back to the main list
		this.resetDropdown();

		// Calculate widths and move items if necessary
		const navItemElements = Array.from( this.navigationItems.getElementsByClassName( 'secondary-navigation__navigation-item' ) );

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
		if ( ! this.moreDropdown || ! this.navigationItems || ! this.moreMenu ) {
			// No, bail early.
			return;
		}

		// Check for first child.
		while ( this.moreDropdown.firstChild ) {
			// Insert the item.
			this.navigationItems.insertBefore( this.moreDropdown.firstChild, this.moreMenu );
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

		// Ensure only one item is active at a time.
		this.clearActiveItems();

		// Add class to the current active content item.
		itemElement.classList.add( 'secondary-navigation__navigation-item--active' );
	}

	/**
	 * Remove active class from all content items.
	 */
	clearActiveItems() {
		// Check for all content items.
		if ( this.allContentItems ) {
			// Loop through all content items and remove the active class.
			this.allContentItems.forEach( ( item ) => {
				// Remove the active class.
				item.classList.remove( 'secondary-navigation__navigation-item--active' );
			} );
		}
	}

	/**
	 * Scroll left on mobile.
	 *
	 * @param {HTMLElement | Element} item The item to scroll into view.
	 * @return {null|void} Null.
	 */
	scrollLeftOnMobile( item: HTMLElement | Element ): null | void {
		// Check for mobile view only.
		if ( ! this.isMobile() ) {
			// No, bail early.
			return;
		}

		// Ensure navigationWrapper exists.
		if ( ! this.navigationWrapper ) {
			// No, bail early.
			return;
		}

		// Get the position of the clicked item relative to the viewport.
		const itemRect = item.getBoundingClientRect();
		const wrapperRect = this.navigationWrapper.getBoundingClientRect();

		// Calculate the offset needed to center the item within the wrapper.
		const itemCenterOffset = itemRect.left - wrapperRect.left + ( itemRect.width / 2 );
		const wrapperCenter = wrapperRect.width / 2;
		const scrollPosition = this.navigationWrapper.scrollLeft + ( itemCenterOffset - wrapperCenter );

		// Scroll to the calculated position to center the item.
		this.navigationWrapper.scrollTo( {
			left: scrollPosition,
			behavior: 'smooth',
		} );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-secondary-navigation', SecondaryNavigation );
