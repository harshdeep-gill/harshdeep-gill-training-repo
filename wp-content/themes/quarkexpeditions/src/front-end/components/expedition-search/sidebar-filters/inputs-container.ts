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
import { filtersMarkupUpdated, compactFiltersMarkupUpdated } from '../actions';

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
	public isCompact: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.lastOpenedAccordionItemId = '';
		this.accordionItems = this.querySelectorAll( '.accordion__item' );
		this.isCompact = !! this.getAttribute( 'compact' );

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
		const { updateFiltersMarkup, filtersMarkup, updateCompactFiltersMarkup, compactFiltersMarkup } = state;

		// Check the type of filters and update accordingly.
		if ( this.isCompact && updateCompactFiltersMarkup && compactFiltersMarkup ) {
			this.innerHTML = compactFiltersMarkup;
		} else if ( ! this.isCompact && updateFiltersMarkup && filtersMarkup ) {
			this.innerHTML = filtersMarkup;
		} else {
			// Bail out.
			return;
		}

		// Get the items.
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

		// Check and fire action accordingly.
		if ( this.isCompact ) {
			compactFiltersMarkupUpdated();
		} else {
			filtersMarkupUpdated();
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
