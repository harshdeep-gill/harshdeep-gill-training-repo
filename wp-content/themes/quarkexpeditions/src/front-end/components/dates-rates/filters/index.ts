/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency.
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * Dates Filters Class.
 */
export default class DatesRatesFilters extends HTMLElement {
	/**
	 * Properties.
	 */
	private filterButton: HTMLButtonElement | null;
	private drawerAccordionItems: NodeListOf<TPAccordionItemElement> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize Parent.
		super();

		// Elements.
		this.filterButton = this.querySelector( '.dates-rates__filter-chip-button' );
		this.drawerAccordionItems = document.querySelectorAll( '.dates-rates__drawer .accordion__item' );

		// Events.
		this.filterButton?.addEventListener( 'click', this.openDrawer.bind( this ) );
	}

	/**
	 * Open drawer.
	 */
	openDrawer() {
		// Close all accordion items.
		this.closeAllAccordionItems();

		// Set the ID.
		const filterButtonID = this.filterButton?.getAttribute( 'accordion_id' );

		// Get the accordion item.
		const accordionItem: TPAccordionItemElement | null = document.querySelector( `#${ filterButtonID }` );

		// Check if accordion item is not available, return.
		if ( ! accordionItem ) {
			// Accordion item not found, bail early.
			return;
		}

		// Open the accordion item.
		setTimeout( () => {
			// Set attribute open to yes.
			accordionItem.setAttribute( 'open', 'yes' );
		}, 600 );
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
		this.drawerAccordionItems.forEach( ( ( item: TPAccordionItemElement ) => {
			// Close items.
			item.close();
		} ) );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-dates-rates-filter', DatesRatesFilters );
