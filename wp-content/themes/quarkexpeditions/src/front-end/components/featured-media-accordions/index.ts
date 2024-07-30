/**
 * Global variables.
 */
const { HTMLElement, customElements } = window;

/**
 * External Dependencies.
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * class FeaturedMediaAccordions.
 */
export class FeaturedMediaAccordions extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly accordionItems : NodeListOf<TPAccordionItemElement>;
	private readonly linkedImagesMap: Map<string, HTMLElement>;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Get accordion items.
		this.accordionItems = this.querySelectorAll( 'tp-accordion-item' );
		this.linkedImagesMap = new Map<string, HTMLElement>();

		// Loop through the items.
		this.accordionItems.forEach( this.setupAccordionItem.bind( this ) );
	}

	/**
	 * Hides all the images.
	 */
	hideAllLinkedImages(): void {
		// Go through all the image elements.
		this.linkedImagesMap.forEach( ( linkedImage ) => {
			// Set the hidden attribute.
			linkedImage.setAttribute( 'data-hidden', 'yes' );
		} );
	}

	/**
	 * Shows a specific image.
	 *
	 * @param { string } imageId The id of the details element.
	 */
	showLinkedImage( imageId: string ): void {
		// Show the details element.
		this.linkedImagesMap.get( imageId )?.removeAttribute( 'data-hidden' );
	}

	/**
	 * Sets up an accordion item.
	 *
	 * @param { TPAccordionItemElement } item
	 * @param { number }                 index
	 */
	setupAccordionItem( item: TPAccordionItemElement, index: number ): void {
		// Get the Id
		const accordionItemId = item.id;

		// Check if the id is empty.
		if ( ! accordionItemId ) {
			// It is, bail.
			return;
		}

		// Get the linked image.
		const linkedImage = this.querySelector<HTMLElement>( `.featured-media-accordions__image[data-accordion-id="${ accordionItemId }"` );

		// Did we get an image?
		if ( ! linkedImage ) {
			// We didn't, bail.
			return;
		}

		// Set the image pair.
		this.linkedImagesMap.set( accordionItemId, linkedImage );

		// Is it the first item?
		if ( 0 === index ) {
			// Yes, show the image by default.
			this.showLinkedImage( accordionItemId );
		}

		// Add event listener.
		item.addEventListener( 'click', () => {
			// Hide all images.
			this.hideAllLinkedImages();

			// Show the linked image.
			this.showLinkedImage( accordionItemId );

			// Close other accordions
			this.accordionItems.forEach( ( accordionItem ) => {
				// Check if it is the same.
				if ( accordionItem.id !== accordionItemId ) {
					// Close the accordion item.
					accordionItem.removeAttribute( 'open' );
				}
			} );
		} );
	}
}

/**
 * Initialize
 */
customElements.define( 'quark-featured-media-accordions', FeaturedMediaAccordions );
