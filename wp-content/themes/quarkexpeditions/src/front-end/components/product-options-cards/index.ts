/**
 * Global variables.
 */
const { HTMLElement, customElements, addEventListenerWithYieldToMain } = window;

/**
 * Internal dependencies.
 */
import { debounce, slideElementDown, slideElementUp } from '../../global/utility';

/**
 * class ProductOptionsCards.
 */
export class ProductOptionsCards extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly cards: NodeListOf<HTMLElement>;
	private readonly cardDetails: NodeListOf<HTMLElement>;
	private readonly cardDetailsMap: Map<string, HTMLElement>;
	private readonly moreDetailsElement: HTMLElement | null;
	private dialogElements: NodeListOf <HTMLDialogElement>;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Get the cards
		this.cards = this.querySelectorAll( '.product-options-cards__card' );
		this.cardDetails = this.querySelectorAll( '.product-options-cards__card-details' );
		this.cardDetailsMap = new Map<string, HTMLElement>();
		this.moreDetailsElement = this.querySelector( '.product-options-cards__more-details' );
		this.dialogElements = document.querySelectorAll( '.dialog' ) as NodeListOf <HTMLDialogElement>;

		// Setup cards.
		this.cards.forEach( this.setupCard.bind( this ) );
		this.cardDetails.forEach( this.setCheckoutURL.bind( this ) );

		// Events.
		window.addEventListener( 'resize', debounce( this.handleDialogClose.bind( this ), 10 ), { passive: true } );
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

		// Remove attribute.
		this.cards.forEach( ( card ) => {
			// Remove attribute.
			card.removeAttribute( 'data-open' );
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

		// Scroll cards into view.
		this?.scrollIntoView();
	}

	/**
	 * Shows a card details element.
	 *
	 * @param { string } detailsId The dom id of the details element.
	 */
	showCardDetailsElement( detailsId: string ): void {
		// Get the details element.
		const detailsElement = this.cardDetailsMap.get( detailsId );

		// Show the details element.
		detailsElement?.removeAttribute( 'data-hidden' );

		// Scroll details into view.
		detailsElement?.scrollIntoView();
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
		hideDetailsButton?.addEventListener( 'click', () => {
			// Check if we have moreDetailsElement.
			if ( ! this.moreDetailsElement ) {
				// We didn't, bail.
				return;
			}

			// Hide the card details.
			slideElementUp( this.moreDetailsElement, 300, () => {
				// Hide.
				this.hideCardDetailsElement( detailsId );
				card.removeAttribute( 'data-open' );
			} );
		} );

		// Event listener for card.
		addEventListenerWithYieldToMain( card, 'click', () => {
			// Check if card is open.
			const isOpen = card.hasAttribute( 'data-open' );

			// Check if already open.
			if ( isOpen ) {
				// Check if we have moreDetailsElement.
				if ( ! this.moreDetailsElement ) {
					// We didn't, bail.
					return;
				}

				// Hide the element.
				slideElementUp( this.moreDetailsElement, 300, () => {
					// Hide.
					this.hideCardDetailsElement( detailsId );
					card.removeAttribute( 'data-open' );
				} );
			} else {
				// Check if we have moreDetailsElement.
				if ( ! this.moreDetailsElement ) {
					// We didn't, bail.
					return;
				}

				// Hide all details.
				this.hideAllCardDetailsElements();

				// Show the details.
				this.showCardDetailsElement( detailsId );
				card.setAttribute( 'data-open', 'yes' );
				slideElementDown( this.moreDetailsElement );
			}
		} );

		// Get the gallery.
		const cardGallery = card.querySelector( '.product-options-cards__gallery' );

		// Check if gallery is available.
		if ( cardGallery ) {
			cardGallery.addEventListener( 'click', ( evt: Event ) => evt.stopPropagation() );
		}
	}

	/**
	 * Setup checkout URL functionality for the card.
	 *
	 * @param { HTMLElement } card
	 */
	setCheckoutURL( card: HTMLElement ): void {
		// Get the all input radio buttons.
		card.querySelectorAll( '.product-options-cards__room input[type="radio"]' ).forEach( ( radioElement ) => {
			// Cast to HTMLInputElement
			const radioInput = radioElement as HTMLInputElement;

			// Click event
			radioInput.addEventListener( 'click', () => {
				// Check if redio checked.
				if ( radioInput.checked ) {
					// Get the checkout URL from the selected radio button's value.
					const checkoutUrl = radioInput.value;

					// Find the 'Book Expedition Now' button within the same card container
					const bookNowButton = card.querySelector( '.product-options-cards__cta-book-now' ) as HTMLAnchorElement;

					// Update the button's href with the new checkout URL
					if ( bookNowButton && checkoutUrl ) {
						bookNowButton.href = checkoutUrl;
					}
				}
			} );
		} );
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
	handleDialogClose() {
		// Check the window width
		if ( ! this.isMobile() ) {
			// Foreach loop.
			this.dialogElements.forEach( ( dialogElement: HTMLDialogElement ) => {
				// Check if modal opened.
				if ( dialogElement.hasAttribute( 'open' ) ) {
					// Close dialog.
					dialogElement?.close();

					// Toggle open attribute.
					dialogElement?.parentElement?.toggleAttribute( 'open' );

					// Remove scroll from body.
					document.querySelector( 'body' )?.classList?.remove( 'prevent-scroll' );
				}
			} );
		}
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-product-options-cards', ProductOptionsCards );
