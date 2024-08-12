/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal Dependency.
 */
import { debounce } from '../../global/utility';

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
	private adventuresContainer: HTMLElement | null;
	private adventuresItems: NodeListOf<Element> | null;
	private adventurescountWrap: HTMLElement | null;
	private adventurescountSpan: HTMLElement | null;
	private moreDetails: HTMLElement | null;
	private dropdownButton: HTMLButtonElement | null;

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
		this.adventuresContainer = this.querySelector( '.departure-cards__options-list' );
		this.adventuresItems = this.querySelectorAll( '.departure-cards__option' );
		this.adventurescountWrap = this.querySelector( '.departure-cards__options-count-wrap' );
		this.adventurescountSpan = this.querySelector( '.departure-cards__options-count' );
		this.dropdownButton = this.querySelector( '.departure-cards__cta' );
		this.moreDetails = this.querySelector( '.departure-cards__more-details' );

		// Events.
		this.updateOfferHiddenItems();
		window.addEventListener( 'resize', debounce( this.updateOfferHiddenItems.bind( this ), 10 ), { passive: true } );
		this.updateAdventuresHiddenItems();
		window.addEventListener( 'resize', debounce( this.updateAdventuresHiddenItems.bind( this ), 10 ), { passive: true } );
		this.dropdownButton?.addEventListener( 'click', this.toggle.bind( this ) );
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
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'open' attribute.
		this.removeAttribute( 'open' );
		this.moreDetails?.classList.remove( 'departure-cards__more-details--active' );
	}

	/**
	 * Toggle the visibility of the "items" based on the presence of overflow items.
	 */
	updateOfferHiddenItems(): void {
		// Check if the offers exists.
		if ( ! this.offers ) {
			// Return if element does not exits.
			return;
		}

		// Set the variables.
		let hiddenCount: number = 0;
		let totalWidth: number = 0;

		// For each item.
		this.offers.forEach( ( offer, index ) => {
			// Remove class.
			offer.classList.remove( 'departure-cards__offer--hidden' );
			totalWidth += offer.clientWidth;

			// Check if the offers list exists.
			if ( ! this.offersList || ! this.offers ) {
				// Return if element does not exits.
				return;
			}

			// Check for the width.
			if ( totalWidth > this.offersList.clientWidth - 100 && index < this.offers.length - 1 ) {
				// Add class.
				offer.classList.add( 'departure-cards__offer--hidden' );
				hiddenCount++;
			}
		} );

		// Check for count span.
		if ( this.offerCountSpan ) {
			// Set the count span.
			this.offerCountSpan.textContent = hiddenCount.toString();
		}

		// Check if the count button exists.
		if ( this.offerCountButton ) {
			// Check hidden count.
			if ( hiddenCount > 0 ) {
				// Remove class from the button.
				this.offerCountButton.classList.remove( 'departure-cards__offer-count-button--hidden' );
			} else {
				// Add class from the button.
				this.offerCountButton.classList.add( 'departure-cards__offer-count-button--hidden' );
			}
		}
	}

	/**
	 * Toggle the visibility of the "items" based on the presence of overflow items.
	 */
	updateAdventuresHiddenItems(): void {
		// Check if the offers exists.
		if ( ! this.adventuresContainer ) {
			// Return if element does not exits.
			return;
		}

		// Set the variables.
		let hiddenCount = 0;
		let totalWidth = 0;

		// For each item.
		this.adventuresItems?.forEach( ( option, index ) => {
			// Check if the offers list exists.
			if ( ! this.adventuresContainer || ! this.adventuresItems || ! this.adventurescountWrap ) {
				// Return if elements does not exits.
				return;
			}

			// Remove the class and set the total width.
			option.classList.remove( 'departure-cards__option--hidden' );
			totalWidth += option.clientWidth;

			// Check for width.
			if ( totalWidth > this.adventuresContainer.clientWidth - this.adventurescountWrap.clientWidth && index < this.adventuresItems.length - 1 ) {
				// Set the class.
				option.classList.add( 'departure-cards__option--hidden' );
				hiddenCount++;
			}
		} );

		// Check for elements.
		if ( ! this.adventurescountWrap || ! this.adventurescountSpan ) {
			// Return if elements does not exits.
			return;
		}

		// Check for hidden count.
		if ( hiddenCount > 0 ) {
			// Set the text and remove hidden class.
			this.adventurescountSpan.textContent = hiddenCount.toString();
			this.adventurescountWrap.classList.remove( 'departure-cards__options-count-wrap--hidden' );
		} else {
			// Add hidden class.
			this.adventurescountWrap.classList.add( 'departure-cards__options-count-wrap--hidden' );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-departure-card', DepartureCard );
