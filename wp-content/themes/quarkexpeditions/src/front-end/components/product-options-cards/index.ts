/**
 * Global variables.
 */
const { HTMLElement, customElements } = window;

/**
 * class ProductOptionsCards.
 */
export class ProductOptionsCards extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly cards : NodeListOf<HTMLElement>;
	private readonly cardDetailsMap: Map<string, HTMLElement>;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Get the cards
		this.cards = this.querySelectorAll( '.product-options-cards__card' );
		this.cardDetailsMap = new Map<string, HTMLElement>();

		// Setup cards.
		this.cards.forEach( this.setupCard.bind( this ) );
	}

	/**
	 * Hides all the more details.
	 */
	hideAllCardDetailsElements(): void {
		// Go through all the card details elements.
		this.cardDetailsMap.forEach( ( detailsElement ) => {
			// Set the hidden attribute.
			detailsElement.setAttribute( 'data-hidden', 'yes' );
		} );
	}

	/**
	 * Hides a specific details element.
	 *
	 * @param { string } detailsId The dom id of the details element.
	 */
	hideCardDetailsElement( detailsId: string ): void {
		// Hide the details element.
		this.cardDetailsMap.get( detailsId )?.setAttribute( 'data-hidden', 'yes' );
	}

	/**
	 * Shows a card details element.
	 *
	 * @param { string } detailsId The dom id of the details element.
	 */
	showCardDetailsElement( detailsId: string ): void {
		// Show the details element.
		this.cardDetailsMap.get( detailsId )?.removeAttribute( 'data-hidden' );
	}

	/**
	 * Setup a card.
	 *
	 * @param { HTMLElement } card
	 */
	setupCard( card: HTMLElement ): void {
		// Get the details ID.
		const detailsId = card.dataset.detailsId;

		// Check if we have valid detailsId
		if ( ! detailsId ) {
			// We don't, bail.
			return;
		}

		// Get the dom object.
		const relatedDetails = this.querySelector<HTMLElement>( `#${ detailsId }` );

		// Did we get a dom object?
		if ( ! relatedDetails ) {
			// No, we didn't.
			return;
		}

		// Set the card details in the map.
		this.cardDetailsMap.set( detailsId, relatedDetails );

		// Get the hide button.
		const hideDetailsButton = relatedDetails.querySelector( '.product-options-cards__card-details-title' );

		// Set event listeners.
		hideDetailsButton?.addEventListener( 'click', () => this.hideCardDetailsElement( detailsId ) );
		card.addEventListener( 'click', () => {
			// Hide all details.
			this.hideAllCardDetailsElements();
			this.showCardDetailsElement( detailsId );
		} );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-product-options-cards', ProductOptionsCards );
