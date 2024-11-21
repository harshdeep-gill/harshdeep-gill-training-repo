/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Expedition Card.
 */
export default class ExpeditionCard extends HTMLElement {
	/**
	 * Properties.
	 */
	private moreDetails: HTMLElement | null;
	private dropdownButton: HTMLButtonElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Elements.
		this.dropdownButton = this.querySelector( '.expedition-cards__cta' );
		this.moreDetails = this.querySelector( '.expedition-cards__more-details' );

		// Events.
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
