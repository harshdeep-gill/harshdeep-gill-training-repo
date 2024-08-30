/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal Dependency.
 */
import { debounce } from '../../global/utility';

/**
 * Expedition Card.
 */
export default class ExpeditionCard extends HTMLElement {
	/**
	 * Properties.
	 */
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
		this.adventuresContainer = this.querySelector( '.expedition-cards__options-list' );
		this.adventuresItems = this.querySelectorAll( '.expedition-cards__option' );
		this.adventurescountWrap = this.querySelector( '.expedition-cards__options-count-wrap' );
		this.adventurescountSpan = this.querySelector( '.expedition-cards__options-count' );
		this.dropdownButton = this.querySelector( '.expedition-cards__cta' );
		this.moreDetails = this.querySelector( '.expedition-cards__more-details' );

		// Events.
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
		this.moreDetails?.classList.add( 'expedition-cards__more-details--active' );
	}

	/**
	 * Close dropdown.
	 */
	close() {
		// Remove 'open' attribute.
		this.removeAttribute( 'open' );
		this.moreDetails?.classList.remove( 'expedition-cards__more-details--active' );
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
			option.classList.remove( 'expedition-cards__option--hidden' );
			totalWidth += option.clientWidth;

			// Check for width.
			if ( totalWidth > this.adventuresContainer.clientWidth - this.adventurescountWrap.clientWidth && index < this.adventuresItems.length - 1 ) {
				// Set the class.
				option.classList.add( 'expedition-cards__option--hidden' );
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
			this.adventurescountWrap.classList.remove( 'expedition-cards__options-count-wrap--hidden' );
		} else {
			// Add hidden class.
			this.adventurescountWrap.classList.add( 'expedition-cards__options-count-wrap--hidden' );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-expedition-card', ExpeditionCard );
