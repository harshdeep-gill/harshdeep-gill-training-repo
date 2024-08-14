/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * Internal dependency.
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * Dates Filters Class.
 */
export default class DatesFilters extends HTMLElement {
	/**
	 * Properties.
	 */
	private filterButton: HTMLButtonElement | null;
	private drawerElement: HTMLElement | null;
	private drawerAccordionItems: NodeListOf<Element> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize Parent.
		super();

		// Elements.
		this.filterButton = this.querySelector( '.dates-rates__filter-button' );
		this.drawerElement = document.querySelector( '.dates-rates__drawer' );
		this.drawerAccordionItems = document.querySelectorAll( '.dates-rates__drawer .accordion__item' );

		// Events.
		this.filterButton?.addEventListener( 'click', this.openDrawer.bind( this ) );
		this.drawerElement?.addEventListener( 'open', this.openAccordionItemByID.bind( this ) );
		this.drawerElement?.addEventListener( 'close', this.closeAllAccordionItems.bind( this ) );
	}

	/**
	 * Open drawer.
	 */
	openDrawer() {
		// Set the ID.
		const filterButtonID = this.filterButton?.getAttribute( 'accordion_id' );

		// Get the accordion item.
		const accordionItem: TPAccordionItemElement | null = document.querySelector( `#${ filterButtonID }` );

		// Check if accordion item is not available, return.
		if ( ! accordionItem ) {
			// Accordion item not found, bail early.
			return;
		}

		// Open the accordion item that is in hash.
		accordionItem.open();
	}

	/**
	 * Open accordion by hash in URL.
	 *
	 * @param {any} itemID - The ID of the accordion item to be opened.
	 */
	openAccordionItemByID( itemID: any ): void {
		// Check if hash is not available, return.
		if ( ! itemID ) {
			// Hash not found, bail early.
			return;
		}

		// Get the accordion item.
		const accordionItem: TPAccordionItemElement | null = this.querySelector( `tp-accordion-item#${ itemID }` );

		// Check if accordion item is not available, return.
		if ( ! accordionItem ) {
			// Accordion item not found, bail early.
			return;
		}

		// Open the accordion item that is in hash.
		accordionItem.open();
	}

	/**
	 * Close accordion inside drawer.
	 */
	closeAllAccordionItems() {
		// Check if items are present.
		if ( ! this.drawerAccordionItems ) {
			// No, bail.
			return;
		}

		// For each item.
		this.drawerAccordionItems.forEach( ( item ) => {
			// Remove attribute open from all items.
			item.removeAttribute( 'open' );
		} );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-filter', DatesFilters );
