/**
 * Global variables
 */
const { HTMLElement, zustand, customElements } = window;

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

		/**
		 * Setup on content load event
		 * This has been used instead of window.onload because window.onload can be overwritten.
		 */
		window.addEventListener( 'load', this.setupSubscription.bind( this ) );
	}

	/**
	 * Updates the component
	 *
	 * @param { Object } state The state object.
	 */
	update( state: SearchFiltersBarState ) {
		// Get the state
		const { history } = state;

		// Null checks.
		if ( ! this.recentSearchCardTemplate ) {
			// Bail.
			return;
		}

		// Log the state.
		console.log( history );
	}

	/**
	 * Sets up the store subscription after content is loaded to prevent undefined errors.
	 */
	setupSubscription() {
		// Get the subscribe function.
		const { subscribe } = zustand.stores.searchFiltersBar;

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}
}

// Define the element
customElements.define( 'quark-expedition-search-recent-searches', QuarkExpeditionSearchRecentSearches );
