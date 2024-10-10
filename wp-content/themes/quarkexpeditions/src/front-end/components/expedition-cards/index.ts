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

		// Run after DOM Content Loaded.
		document.addEventListener( 'DOMContentLoaded', () => {
			// Run after DOM Content Loaded.
			this.updateAdventuresHiddenItems();
		} );

		// Events.
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
	 * Toggle the visibility of the "items" in adventures based on screen size.
	 * On mobile, hide all except the first item.
	 */
	updateAdventuresHiddenItems(): void {
		// Check if the element exists.
		if ( ! this.adventuresContainer ) {
			// Return.
			return;
		}

		// Set hidden count and total width.
		let hiddenCount = 0;
		let totalWidth = 0;

		// Set is mobile variable.
		const isMobile = window.innerWidth < 576;

		// For each loop.
		this.adventuresItems?.forEach( ( option, index ) => {
			// Remove hidden class.
			option.classList.remove( 'expedition-cards__option--hidden' );

			// On mobile, only show the first item.
			if ( isMobile ) {
				// Check for first item.
				if ( index === 0 ) {
					// Set the total width.
					totalWidth += option.clientWidth;

					// Always show the first item.
					return;
				}

				// Add hidden class.
				option.classList.add( 'expedition-cards__option--hidden' );
				hiddenCount++;
			} else {
				// Default behavior for larger screens.
				totalWidth += option.clientWidth;

				// Width check.
				if ( totalWidth > this.adventuresContainer!.clientWidth - this.adventurescountWrap!.clientWidth ) {
					// Add hidden class
					option.classList.add( 'expedition-cards__option--hidden' );
					hiddenCount++;
				}
			}
		} );

		// Update count and toggle visibility.
		if ( hiddenCount > 0 ) {
			// Set the text and remvoe hidden class.
			this.adventurescountSpan!.textContent = hiddenCount.toString();
			this.adventurescountWrap!.classList.remove( 'expedition-cards__options-count-wrap--hidden' );
		} else {
			// Add class.
			this.adventurescountWrap!.classList.add( 'expedition-cards__options-count-wrap--hidden' );
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-expedition-card', ExpeditionCard );
