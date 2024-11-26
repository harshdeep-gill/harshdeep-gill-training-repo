/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependencies
 */
import { TPModalElement } from '@travelopia/web-components';

/**
 * Internal Dependency.
 */
import { debounce } from '../../global/utility';

/**
 * Itinerary Tabs Class.
 */
export default class ItineraryTabs extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private modalElements: NodeListOf <TPModalElement>;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.modalElements = document.querySelectorAll( '.itinerary-details__modal' ) as NodeListOf <TPModalElement>;

		// Events.
		window.addEventListener( 'resize', debounce( this.handleModalClose.bind( this ), 10 ), { passive: true } );
	}

	/**
	 * Check if it's mobile view.
	 */
	isMobile() {
		// Return true if screen is mobile.
		return 768 > window.innerWidth;
	}

	/**
	 * Handle modal close.
	 */
	handleModalClose() {
		// Check the window width
		if ( ! this.isMobile() ) {
			// Foreach loop.
			this.modalElements.forEach( ( modalElement: TPModalElement ) => {
				// Check if modal opened.
				if ( modalElement.getAttribute( 'open' ) === 'yes' ) {
					// Close the modal.
					modalElement?.close();
				}
			} );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-itinerary-tabs', ItineraryTabs );
