/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * External dependency.
 */
import { TPAccordionHandleElement } from '@travelopia/web-components';

/**
 * Internal Dependency.
 */
import { debounce } from '../../../global/utility';

/**
 * Get the store.
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Dates Filter Class.
 */
export default class DatesRatesFilterChipElement extends HTMLElement {
	/**
	 * Properties.
	 */
	private filterButton: HTMLButtonElement | null;
	private drawerAccordionHandles: NodeListOf<TPAccordionHandleElement> | null;
	private readonly filterType: string;

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

		// Event for accordion items.
		this.drawerAccordionHandles.forEach( ( ( item: TPAccordionHandleElement ) => {
			// Close items.
			item.addEventListener( 'click', this.handleAccordionItems.bind( this ) );
		} ) );

		// Get the filter type.
		this.filterType = this.getAttribute( 'type' ) ?? '';

		// Check if this is a currency filter chip.
		if ( this.filterType && ( 'currency' === this.filterType || 'sticky-filter' === this.filterType ) ) {
			subscribe( this.update.bind( this ) );
		}
	}

	/**
	 * Update the component.
	 *
	 * @param {Object} state
	 */
	update( state: DatesRatesState ) {
		// Get the currency state.
		const { selectedFilters } = state;

		// Handle filter state update.
		this.handleFilterStateUpdate( selectedFilters );
	}

	/**
	 * Handles the change in filter state.
	 *
	 * @param {Object} selectedFilters
	 */
	handleFilterStateUpdate( selectedFilters: DatesRatesFilters ) {
		// Check if this is a currency filter.
		if ( 'currency' === this.filterType ) {
			// Yes, bail.
			return;
		}

		//  Is there a filter?
		let isFiltered = false;

		// Loop through the object.
		for ( const filterName in selectedFilters ) {
			// Check if it is the currency filter.
			if ( 'currency' === filterName ) {
				// Do nothing.
				continue;
			}

			// Check if any filter is active.
			if ( selectedFilters[ filterName ] && selectedFilters[ filterName ].length ) {
				isFiltered = true;
				break;
			}
		}

		// Check if it is filtered.
		if ( isFiltered ) {
			// Yes, add attribute.
			this.setAttribute( 'filtered', 'yes' );
		} else {
			// No, remove attribute.
			this.removeAttribute( 'filtered' );
		}
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
