/**
 * Global variables.
 */
const { customElements, HTMLElement } = window;

/**
 * External dependency.
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * Internal Dependency.
 */
import { debounce } from '../../../global/utility';

/**
 * Dates Filters Class.
 */
export default class DatesRatesFilters extends HTMLElement {
	/**
	 * Properties.
	 */
	private filterButton: HTMLButtonElement | null;
	private clearFilterButton: HTMLButtonElement | null;
	private drawerAccordionItems: NodeListOf<TPAccordionItemElement> | null;
	private checkboxItems: NodeListOf<HTMLInputElement> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize Parent.
		super();

		// Elements.
		this.filterButton = this.querySelector( '.dates-rates__filter-chip-button' );
		this.clearFilterButton = document.querySelector( '.dates-rates__cta-clear-filters' );
		this.drawerAccordionItems = document.querySelectorAll( '.dates-rates__drawer .accordion__item' );
		this.checkboxItems = document.querySelectorAll( '.dates-rates__drawer input[type="checkbox"]' );

		// Events.
		this.filterButton?.addEventListener( 'click', debounce( this.openDrawer.bind( this ), 10 ), { passive: true } );
		this.clearFilterButton?.addEventListener( 'click', this.clearAllCheckboxes.bind( this ) );

		// Event for accordion items.
		this.drawerAccordionItems.forEach( ( ( item: TPAccordionItemElement ) => {
			// Close items.
			item.addEventListener( 'click', this.handleAccordionItems.bind( this ) );
		} ) );
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

		// Set attribute open to yes.
		accordionItem.setAttribute( 'open', 'yes' );
	}

	/**
	 * Close accordion inside drawer.
	 *
	 * @param { HTMLElement | null } skippedItem An item to be skipped while closing everything.
	 */
	closeAllAccordionItems( skippedItem: HTMLElement | null = null ) {
		// Check if items are present.
		if ( ! this.drawerAccordionItems ) {
			// No, bail.
			return;
		}

		// For each item.
		this.drawerAccordionItems.forEach( ( ( item: TPAccordionItemElement ) => {
			// Check if this item should be skipped.
			if ( item === skippedItem ) {
				// This item should not be closed.
				return;
			}

			// Closse the item.
			item.removeAttribute( 'open' );
		} ) );
	}

	/**
	 * Handle accordion inside drawer.
	 *
	 * @param {Event} event
	 */
	handleAccordionItems( event: any ) {
		// Set current item.
		const currentItem = event.currentTarget as TPAccordionItemElement;

		// Open the current item.
		if ( ! currentItem.hasAttribute( 'open' ) ) {
			currentItem.setAttribute( 'open', 'yes' );
		} else {
			currentItem.removeAttribute( 'open' );

			// No need to close other items.
			return;
		}

		// Close all accordion items.
		this.closeAllAccordionItems( currentItem );
	}

	/**
	 * Clear checkboxes.
	 */
	clearAllCheckboxes() {
		// Uncheck each checkbox
		this.checkboxItems?.forEach( ( checkbox ) => {
			// Remove checked from checkboxe.
			checkbox.checked = false;
		} );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-dates-rates-filter', DatesRatesFilters );
