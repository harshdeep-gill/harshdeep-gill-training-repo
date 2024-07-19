/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency.
 */
import { TPSliderElement } from '@travelopia/web-components';

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

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.sliders = this.querySelectorAll( 'tp-slider' );

		/**
		 * Update all slider heights on initialization.
		 *
		 * Otherwise the sliders look broken.
		 */
		this.sliders?.forEach( ( slider: TPSliderElement ): void => {
			// Update slider height.
			slider.updateHeight();
		} );

		// Events.
		this.addEventListener( 'change', this.updateChildrenTabs.bind( this ) );
	}

	// Update children tab.
	updateChildrenTabs(): void {
		// const innerTabs = this.querySelector( '.tabs__tab tp-tabs' );

		// console.log( 'Update children', this);
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-tabs', Tabs );
