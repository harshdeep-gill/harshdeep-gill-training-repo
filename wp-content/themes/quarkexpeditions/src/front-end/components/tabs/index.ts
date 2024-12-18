/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Tabs Class.
 */
export default class Tabs extends HTMLElement {
	/**
	 * Properties.
	 */
	private navigationWrapper: HTMLElement | null;
	private tabsNavItems: NodeListOf<HTMLElement>;
	private isMobile: boolean;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.navigationWrapper = this.querySelector( '.tabs__nav' );
		this.tabsNavItems = this.querySelectorAll( 'tp-tabs-nav-item' );

		this.isMobile = window.innerWidth <= 1024;

		// Initialize event listeners.
		this.tabsNavItems?.forEach( ( tabItem: HTMLElement ): void => {
			// Click event.
			tabItem.addEventListener( 'click', ( event ) => {
				// Scroll to the center.
				this.scrollToCenter( event.currentTarget as HTMLElement );
			} );
		} );
	}

	/**
	 * Scroll to center of the wrapper on mobile when a tab is clicked.
	 *
	 * @param {HTMLElement} item The item to scroll into view.
	 * @return {void}
	 */
	scrollToCenter( item: HTMLElement ): void {
		// Check for mobile view only.
		if ( ! this.isMobile ) {
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
 * Initialize custom element.
 */
customElements.define( 'quark-tabs', Tabs );
