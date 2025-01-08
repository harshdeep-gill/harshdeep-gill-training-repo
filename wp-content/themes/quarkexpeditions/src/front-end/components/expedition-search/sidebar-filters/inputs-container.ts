/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * External dependencies
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * Internal dependencies.
 */
import { filtersMarkupUpdated } from '../actions';

/**
 * Store
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * Filters Inputs Container class
 */
export default class ExpeditionSearchSidebarFiltersInputsContainerElement extends HTMLElement {
	/**
	 * Properties
	 */
	private lastOpenedAccordionItemId: string;
	private accordionItems: NodeListOf<TPAccordionItemElement> | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.lastOpenedAccordionItemId = '';
		this.accordionItems = this.querySelectorAll( '.accordion__item' );

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
	update( state: ExpeditionSearchState ) {
		// Get the state.
		const { updateFiltersMarkup, filtersMarkup } = state;

		// Should we update filters markup?
		if ( ! ( updateFiltersMarkup && filtersMarkup ) ) {
			// Nope, Bail.
			return;
		}

		// Update the markup.
		this.innerHTML = filtersMarkup;
		this.accordionItems = this.querySelectorAll( '.accordion__item' );

		// Setup events.
		this.setupAccordionEvents();

		// Get the accordion item that was opened.
		const accordionItem = document.getElementById( this.lastOpenedAccordionItemId );
		const accordionItemContent = accordionItem?.querySelector( 'tp-accordion-content' );

		// Null check.
		if ( accordionItem && accordionItemContent ) {
			// Add no transition class.
			accordionItemContent.classList.add( 'expedition-search__accordion-content--no-transition' );

			// Open the accordion item.
			accordionItem.setAttribute( 'open', 'yes' );

			// Remove the no transition class.
			accordionItemContent.addEventListener( 'animationend', () => accordionItemContent.classList.remove( 'expedition-search__accordion-content--no-transition' ) );
		}

		// Updated callback.
		filtersMarkupUpdated();
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
		this.accordionItems?.forEach( ( ( item: TPAccordionItemElement ) => {
			// Close items.
			item.addEventListener( 'open', this.handleAccordionItems.bind( this ) );
		} ) );
	}

	/**
	 * Handle accordion.
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
	 * Close accordion inside.
	 *
	 * @param { HTMLElement | null } skippedItem An item to be skipped while closing everything.
	 */
	closeAllAccordionItems( skippedItem: HTMLElement | null = null ) {
		// Check if items are present.
		if ( ! this.accordionItems ) {
			// No, bail.
			return;
		}

		// For each item.
		this.accordionItems.forEach( ( ( item: TPAccordionItemElement ) => {
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
