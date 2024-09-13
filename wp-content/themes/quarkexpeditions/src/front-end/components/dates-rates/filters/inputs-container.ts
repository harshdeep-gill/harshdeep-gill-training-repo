/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * External dependencies
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Filters Inputs Container class
 */
export default class DatesRatesFiltersInputsContainerElement extends HTMLElement {
	/**
	 * Properties
	 */
	private lastOpenedAccordionItemId: string;
	private drawerAccordionItems: NodeListOf<TPAccordionItemElement> | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.lastOpenedAccordionItemId = '';
		this.drawerAccordionItems = this.querySelectorAll( '.accordion__item' );

		// Handle events
		this.setupAccordionEvents();

		// Subscribe to store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the state.
		const { shouldMarkupUpdate, filtersMarkup } = state;

		// Should we update filters markup?
		if ( ! ( shouldMarkupUpdate && filtersMarkup ) ) {
			// Nope, Bail.
			return;
		}

		// Update the markup.
		this.innerHTML = filtersMarkup;
		this.drawerAccordionItems = this.querySelectorAll( '.accordion__item' );

		// Setup events.
		this.setupAccordionEvents();

		// Get the accordion item that was opened.
		const accordionItem = document.getElementById( this.lastOpenedAccordionItemId );
		const accordionItemContent = accordionItem?.querySelector( 'tp-accordion-content' );

		// Null check.
		if ( accordionItem && accordionItemContent ) {
			// Add no transition class.
			accordionItemContent.classList.add( 'dates-rates__accordion-content--no-transition' );

			// Open the accordion item.
			accordionItem.setAttribute( 'open', 'yes' );

			// Remove the no transition class.
			accordionItemContent.addEventListener( 'animationend', () => accordionItemContent.classList.remove( 'dates-rates__accordion-content--no-transition' ) );
		}
	}

	/**
	 * Sets the last opened accordion id.
	 *
	 * @param {string} accordionItemId The id of the accordion.
	 */
	setLastOpenedAccordionItemId( accordionItemId: string ) {
		// Empty string check.
		if ( ! accordionItemId ) {
			// Bail.
			return;
		}

		// Set the id.
		this.lastOpenedAccordionItemId = accordionItemId;
	}

	/**
	 * Sets up the event listeners for the accordion items.
	 */
	setupAccordionEvents() {
		// Event for accordion items.
		this.drawerAccordionItems?.forEach( ( ( item: TPAccordionItemElement ) => {
			// Close items.
			item.addEventListener( 'open', this.handleAccordionItems.bind( this ) );
		} ) );
	}

	/**
	 * Handle accordion inside drawer.
	 *
	 * @param {Event} event
	 */
	handleAccordionItems( event: Event ) {
		// Set current item.
		const currentItem = event.currentTarget as TPAccordionItemElement | null;

		// Null check.
		if ( ! currentItem ) {
			// Bail.
			return;
		}

		// Is the accordion item open?
		this.closeAllAccordionItems( currentItem );
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

			// Close the item.
			item.close();
			item.removeAttribute( 'open' );
		} ) );
	}
}
