/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependency.
 */
import { TPAccordionHandleElement } from '@travelopia/web-components';

/**
 * Internal Dependency.
 */
import { debounce } from '../../global/utility';

/**
 * Dates Filters Class.
 */
export default class DatesRatesFilters extends HTMLElement {
	/**
	 * Properties.
	 */
	private filterButton: HTMLButtonElement | null;
	private clearFilterButton: HTMLButtonElement | null;
	private drawerAccordionHandles: NodeListOf<TPAccordionHandleElement> | null;
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
		this.drawerAccordionHandles = document.querySelectorAll( '.dates-rates__drawer .accordion__handle' );
		this.checkboxItems = document.querySelectorAll( '.dates-rates__drawer input[type="checkbox"]' );

		// Events.
		this.filterButton?.addEventListener( 'click', debounce( this.openDrawer.bind( this ), 10 ), { passive: true } );
		this.clearFilterButton?.addEventListener( 'click', this.clearAllCheckboxes.bind( this ) );

		// Event for accordion items.
		this.drawerAccordionHandles.forEach( ( ( item: TPAccordionHandleElement ) => {
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
		const accordionItem: TPAccordionHandleElement | null = document.querySelector( `#${ filterButtonID }` );

		// Check if accordion item is not available, return.
		if ( ! accordionItem ) {
			// Accordion item not found, bail early.
			return;
		}

		// Set attribute open to yes.
		this.openAccordion( accordionItem );
	}

	/**
	 * Close accordion inside drawer.
	 *
	 * @param { HTMLElement | null } skippedItem An item to be skipped while closing everything.
	 */
	closeAllAccordionItems( skippedItem: HTMLElement | null = null ) {
		// Check if items are present.
		if ( ! this.drawerAccordionHandles ) {
			// No, bail.
			return;
		}

		// For each item.
		this.drawerAccordionHandles.forEach( ( ( item: TPAccordionHandleElement ) => {
			// Check if this item should be skipped.
			if ( item === skippedItem ) {
				// This item should not be closed.
				return;
			}

			// Closse the item.
			this.closeAccordion( item );
		} ) );
	}

	/**
	 * Handle accordion inside drawer.
	 *
	 * @param {Event} event
	 */
	handleAccordionItems( event: any ) {
		// Set current item.
		const currentItem = event.currentTarget as TPAccordionHandleElement;

		// Open the current item.
		if ( ! currentItem.closest( 'tp-accordion-item' )?.hasAttribute( 'open' ) ) {
			this.openAccordion( currentItem );
		} else {
			this.closeAccordion( currentItem );

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

	/**
	 * Adds `open` attribute to an accordion based on its handle.
	 *
	 * @param { TPAccordionHandleElement } handle The handle of the accordion.
	 */
	openAccordion( handle: TPAccordionHandleElement ) {
		// Set the attribute.
		handle?.closest( 'tp-accordion-item' )?.setAttribute( 'open', 'yes' );
	}

	/**
	 * Removes `open` attribute from an accordion based on its handle.
	 *
	 * @param { TPAccordionHandleElement } handle The handle of the accordion.
	 */
	closeAccordion( handle: TPAccordionHandleElement ) {
		// Set the attribute.
		handle?.closest( 'tp-accordion-item' )?.removeAttribute( 'open' );
	}
}
