/**
 * Globals
 */
const { HTMLElement, zustand } = window;

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

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.lastOpenedAccordionItemId = '';

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
}
