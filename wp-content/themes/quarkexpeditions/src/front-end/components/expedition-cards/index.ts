/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependencies
 */
import { QuarkDrawerElement } from '../drawer/drawer';
import { throttle } from '../../global/utility';

/**
 * Expedition Card.
 */
export default class ExpeditionCard extends HTMLElement {
	/**
	 * Properties.
	 */
	private moreDetails: HTMLElement | null;
	private dropdownButton: HTMLButtonElement | null;
	private adventureOptionsDrawer: QuarkDrawerElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.dropdownButton = this.querySelector( '.expedition-cards__cta' );
		this.moreDetails = this.querySelector( '.expedition-cards__more-details' );
		this.adventureOptionsDrawer = document.getElementById( this.getAttribute( 'aop-drawer-id' ) ?? '' ) as QuarkDrawerElement | null;

		// Events.
		this.dropdownButton?.addEventListener( 'click', this.toggle.bind( this ) );

		// Do we have a drawer?
		if ( this.adventureOptionsDrawer ) {
			window.addEventListener( 'resize', throttle( () => {
				// Check the window width.
				if ( window.innerWidth > 576 && this.adventureOptionsDrawer?.hasAttribute( 'open' ) ) {
					this.adventureOptionsDrawer?.removeAttribute( 'open' );
					document.body.classList.remove( 'prevent-scroll' );
				}
			} ) );

			// Setup open event.
			this.adventureOptionsDrawer.addEventListener( 'open', throttle( () => {
				// Check the window width.
				if ( window.innerWidth > 576 ) {
					this.adventureOptionsDrawer?.removeAttribute( 'open' );
					document.body.classList.remove( 'prevent-scroll' );
				}
			} ) );
		}
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
		this.moreDetails?.classList.add( 'expedition-cards__more-details--active' );

		// Scroll details into view.
		this.moreDetails?.scrollIntoView();
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'open' attribute.
		this.removeAttribute( 'open' );
		this.moreDetails?.classList.remove( 'expedition-cards__more-details--active' );

		// Scroll the current card into view.
		this.scrollIntoView();
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-expedition-card', ExpeditionCard );
