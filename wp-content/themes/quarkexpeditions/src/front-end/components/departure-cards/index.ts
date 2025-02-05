/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal Dependency.
 */
import { throttle } from '../../global/utility';
import { QuarkDrawerElement } from '../drawer/drawer';

/**
 * External Dependency.
 */
const { addEventListenerWithYieldToMain } = window;

/**
 * Departure Card.
 */
export default class DepartureCard extends HTMLElement {
	/**
	 * Properties.
	 */
	private offersList: HTMLElement | null;
	private offers: NodeListOf<Element> | null;
	private offerCountButton: HTMLElement | null;
	private offerCountSpan: HTMLElement | null;
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
		this.offersList = this.querySelector( '.departure-cards__offers-list' );
		this.offers = this.querySelectorAll( '.departure-cards__offer' );
		this.offerCountButton = this.querySelector( '.departure-cards__offer-count-button' );
		this.offerCountSpan = this.querySelector( '.departure-cards__offer-count' );
		this.dropdownButton = this.querySelector( '.departure-cards__cta' );
		this.moreDetails = this.querySelector( '.departure-cards__more-details' );
		this.adventureOptionsDrawer = document.getElementById( this.getAttribute( 'aop-drawer-id' ) ?? '' ) as QuarkDrawerElement | null;

		// Check for existence.
		if ( ! this.dropdownButton ) {
			// Bail.
			return;
		}

		// Events.
		window.addEventListener( 'resize', throttle( this.updateOfferHiddenItems.bind( this ), 10 ), { passive: true } );
		window.addEventListener( 'load', () => {
			// Update the hidden items.
			this.updateOfferHiddenItems();
		} );

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

		// Add event listener.
		addEventListenerWithYieldToMain( this.dropdownButton, 'click', this.toggle.bind( this ) );
	}

	/**
	 * Connected callback.
	 */
	connectedCallback() {
		// Need to do this here as well since some cards may load dynamically on filter change or load more, etc.
		this.updateOfferHiddenItems();
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
		this.moreDetails?.classList.add( 'departure-cards__more-details--active' );

		// Scroll details into view.
		this.moreDetails?.scrollIntoView();
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'open' attribute.
		this.removeAttribute( 'open' );
		this.moreDetails?.classList.remove( 'departure-cards__more-details--active' );

		// Scroll the current card into view.
		this.scrollIntoView();
	}

	/**
	 * Toggle the visibility of the "items" based on screen size.
	 * On mobile, hide all except the first item.
	 */
	updateOfferHiddenItems(): void {
		// Check if the offers exists.
		if ( ! this.offers ) {
			// Return if element does not exist.
			return;
		}

		// Set the variables.
		let hiddenCount: number = 0;
		let totalWidth: number = 0;

		// For mobile layout only (screen width < 576px).
		const isMobile = window.innerWidth < 576;

		// For each item.
		this.offers.forEach( ( offer, index ) => {
			// Remove class.
			offer.classList.remove( 'departure-cards__offer--hidden' );

			// On mobile, only show the first item.
			if ( isMobile ) {
				// Check for first item.
				if ( index === 0 ) {
					// Always show the first item.
					totalWidth += offer.clientWidth;

					// Return.
					return;
				}

				// Add class.
				offer.classList.add( 'departure-cards__offer--hidden' );
				hiddenCount++;
			} else {
				// Default behavior for larger screens.
				totalWidth += offer.clientWidth;

				// Check for the width.
				if ( totalWidth > this.offersList!.clientWidth - 110 ) {
					// Add class to hide the item.
					offer.classList.add( 'departure-cards__offer--hidden' );
					hiddenCount++;
				}
			}
		} );

		// Update the hidden item count.
		if ( this.offerCountSpan ) {
			this.offerCountSpan.textContent = hiddenCount.toString();
		}

		// Toggle visibility of the count button.
		if ( this.offerCountButton ) {
			// On mobile, only show the first item.
			if ( hiddenCount > 0 ) {
				// Remove class.
				this.offerCountButton.classList.remove( 'departure-cards__offer-count-button--hidden' );
			} else {
				// Add class.
				this.offerCountButton.classList.add( 'departure-cards__offer-count-button--hidden' );
			}
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-departure-card', DepartureCard );
