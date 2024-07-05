/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency.
 */
import '@travelopia/web-components/dist/tabs';
import { TPSliderElement, TPTabsElement } from '@travelopia/web-components';

/**
 * Internal dependency.
 */
import { debounce } from '../../global/utility';

/**
 * Tabs Class.
 */
export default class Tabs extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private sliders: NodeListOf<TPSliderElement>;
	private tabs: TPTabsElement | null;
	private tabsContentContainer: HTMLElement | null;
	private firstTabId: string;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.sliders = this.querySelectorAll( 'tp-slider' );
		this.tabs = this.querySelector( 'tp-tabs' );
		this.tabsContentContainer = this.querySelector( '.tabs__content' );
		this.firstTabId = this.querySelector( 'tp-tabs-tab' )?.getAttribute( 'id' ) ?? '';

		/**
		 * Update all slider heights on initialization.
		 *
		 * Otherwise the sliders look broken.
		 */
		this.sliders?.forEach( ( slider: TPSliderElement ): void => {
			// Update slider height.
			slider.updateHeight();
		} );

		// Update tab content height on initial render.
		this.updateTabContentHeight();
	}

	/**
	 * Connected callback.
	 */
	connectedCallback(): void {
		// Add event to update height, on tab change.
		this.tabs?.addEventListener( 'change', () => this.updateTabContentHeight() );

		// Resize observer.
		if ( 'ResizeObserver' in window ) {
			// Update height on content resize.
			new ResizeObserver( this.updateTabContentHeight.bind( this ) ).observe( this );
		} else {
			// Update height on window resize.
			this.ownerDocument.addEventListener( 'resize', debounce( this.updateTabContentHeight.bind( this ) ) );
		}
	}

	/**
	 * Update tab content height.
	 */
	updateTabContentHeight(): void {
		// Check if tab content container exists.
		if ( ! this.tabsContentContainer ) {
			// Early return.
			return;
		}

		// Get current tab.
		const currentTabId = this.tabs?.getAttribute( 'current-tab' );
		let currentTabsContentElement = this.querySelector( `[ id=${ currentTabId } ]` );

		// Check if current tabs content element is available.
		if ( ! currentTabsContentElement ) {
			// Set the current tab value to the first tab
			currentTabsContentElement = currentTabsContentElement ? currentTabsContentElement : this.querySelector( `[ id=${ this.firstTabId } ]` );
			this.setAttribute( 'current-tab', this.firstTabId );
		}

		// Check again if current tabs content element is available.
		if ( ! currentTabsContentElement ) {
			// Return.
			return;
		}

		// Set the height of the container to be the height of the current slide.
		const height: number = currentTabsContentElement.scrollHeight;
		this.tabsContentContainer.style.height = `${ height }px`;
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-tabs', Tabs );
