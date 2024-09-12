/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * External dependency.
 */
import { TPAccordionHandleElement } from '@travelopia/web-components';

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Internal Dependency.
 */
import { debounce } from '../../../global/utility';

/**
 * Dates Filter Class.
 */
export default class DatesRatesFilterChipElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private filterButton: HTMLButtonElement | null;
	private drawerAccordionHandles: NodeListOf<TPAccordionHandleElement> | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize Parent.
		super();

		// Elements.
		this.filterButton = this.querySelector( '.dates-rates__filter-chip-button' );
		this.drawerAccordionHandles = document.querySelectorAll( '.dates-rates__drawer .accordion__handle' );

		// Events.
		this.filterButton?.addEventListener( 'click', debounce( this.openDrawer.bind( this ), 10 ), { passive: true } );

		// this.setupAccordionEvents();

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state State object.
	 */
	update( state: DatesRatesState ) {
		// Check if we should update
		if ( ! state.shouldMarkupUpdate ) {
			// Bail.
			return;
		}

		// Setup the events.
		this.setupAccordionEvents();
	}

	/**
	 * Sets up the event listeners for the accordion handles.
	 */
	setupAccordionEvents() {
		// Event for accordion items.
		this.drawerAccordionHandles?.forEach( ( ( item: TPAccordionHandleElement ) => {
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
		const filterButtonID = this.getAttribute( 'accordion_id' ) ?? '';

		// Get the accordion item.
		const accordionItem = document.getElementById( filterButtonID ) as TPAccordionHandleElement | null;

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
