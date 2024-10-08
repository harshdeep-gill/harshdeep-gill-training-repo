/**
 * Global variables
 */
const { HTMLElement, zustand, customElements } = window;

/**
 * Get the store
 */
const { subscribe } = zustand.stores.searchFiltersBar;

/**
 * Class QuarkExpeditionSearchRecentSearches
 */
export default class QuarkExpeditionSearchRecentSearches extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly recentSearchCardTemplate: HTMLTemplateElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.recentSearchCardTemplate = this.querySelector( 'template' );

		// Subscribe to the store
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component
	 *
	 * @param { Object } state The state object.
	 */
	update( state: SearchFiltersBarState ) {
		// Get the state
		const { initialized, selectedDestinations, selectedMonths } = state;

		// Null checks.
		if ( ! initialized || ! selectedDestinations || ! selectedMonths || ! this.recentSearchCardTemplate ) {
			// Bail.
			return;
		}
	}
}

// Define the element
customElements.define( 'quark-expedition-search-recent-searches', QuarkExpeditionSearchRecentSearches );
