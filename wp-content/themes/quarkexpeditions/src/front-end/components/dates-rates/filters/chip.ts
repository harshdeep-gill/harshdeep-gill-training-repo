/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * External dependencies
 */
import { TPAccordionItemElement } from '@travelopia/web-components';

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

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize Parent.
		super();

		// Elements.
		this.filterButton = this.querySelector( '.dates-rates__filter-chip-button' );

		// Events.
		this.filterButton?.addEventListener( 'click', debounce( this.openDrawer.bind( this ), 10 ), { passive: true } );
	}

	/**
	 * Open drawer.
	 */
	openDrawer() {
		// Set the ID.
		const filterButtonID = this.getAttribute( 'accordion_id' ) ?? '';

		// Get the accordion item.
		const accordionItem = document.getElementById( filterButtonID ) as TPAccordionItemElement | null;

		// Check if accordion item is not available, return.
		if ( ! accordionItem ) {
			// Accordion item not found, bail early.
			return;
		}

		// Set attribute open to yes.
		accordionItem.open();
	}
}
