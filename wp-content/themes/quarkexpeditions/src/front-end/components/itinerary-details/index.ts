/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency.
 */
import '@travelopia/web-components/dist/tabs';
import { TPTabsElement } from '@travelopia/web-components';

/**
 * Itinerary Tabs Class.
 */
export default class ItineraryTabs extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private tabsNavItems: NodeListOf<HTMLElement>;
	private tabsTabItems: NodeListOf<HTMLElement>;
	private tabs: TPTabsElement | null;
	private closeButtons: NodeListOf<HTMLElement>;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.tabsNavItems = this.querySelectorAll( 'tp-tabs-nav-item' );
		this.tabsTabItems = this.querySelectorAll( 'tp-tabs-tab' );
		this.tabs = this.querySelector( 'tp-tabs' );
		this.closeButtons = this.querySelectorAll( '.itinerary-details__button-close' );

		// Update tabs.
		this.updateTabStatusForMobile();

		// Event for close button.
		this.closeButtons?.forEach( ( closeButton ): void => {
			// Update tabs.
			closeButton?.addEventListener( 'click', this.updateTabStatusForMobile.bind( this ) );
		} );
	}

	/**
	 * Check if it's mobile view.
	 */
	isMobile() {
		// Return true if screen is mobile.
		return 768 >= window.innerWidth;
	}

	/**
	 * Update tab status.
	 */
	updateTabStatusForMobile(): void {
		// Check for the screen size.
		if ( ! this.isMobile() ) {
			// No, bail early.
			return;
		}

		// Remove 'current-tab' attribute.
		this.tabs?.removeAttribute( 'current-tab' );

		/**
		 * Remove 'active' attribute from all nav items.
		 */
		this.tabsNavItems?.forEach( ( tabsNavItem ): void => {
			// Remove 'active' attribute.
			tabsNavItem.removeAttribute( 'active' );
		} );

		/**
		 * Remove 'open' attribute from all tab items.
		 */
		this.tabsTabItems?.forEach( ( tabsTabItem ): void => {
			// Remove 'open' attribute.
			tabsTabItem.removeAttribute( 'open' );
		} );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-itinerary-tabs', ItineraryTabs );
