/**
 * Global variables
 */
const { HTMLElement, zustand, customElements } = window;

/**
 * Get the expedition search store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * Internal dependencies
 */
import { addDestination, addMonth, clearAllFilters, removeDestination, removeMonth } from '../actions';

/**
 * Class QuarkExpeditionSearchRecentSearches
 */
export default class QuarkExpeditionSearchRecentSearches extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly recentSearchCardTemplate: HTMLTemplateElement | null;
	private readonly recentSearchCardsContainer: HTMLElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.recentSearchCardTemplate = this.querySelector( 'template' );
		this.recentSearchCardsContainer = this.querySelector( '.mini-cards-list' );

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
		if ( ! this.recentSearchCardTemplate || ! history || ! Array.isArray( history ) ) {
			// Bail.
			return;
		}

		// Loop through the history.
		history.forEach( ( historyItem ) => {
			// Get the destination and month
			const { destination, month } = historyItem;

			// Null check
			if (
				! destination ||
				! month ||
				! destination.value ||
				! destination.label ||
				! destination.imageUrl ||
				! month.label ||
				! month.value ||
				! this.recentSearchCardTemplate ||
				! this.recentSearchCardsContainer
			) {
				// Bail.
				return;
			}

			// Get the template clone.
			const templateClone = this.recentSearchCardTemplate.content.cloneNode( true ) as HTMLElement;

			// Get the card and its children elements.
			const cardImageElement = templateClone.querySelector( 'img' );
			const cardTitleElement = templateClone.querySelector( '.mini-cards-list__card-title' );
			const cardDateElement = templateClone.querySelector( '.mini-cards-list__card-date' );
			const cardElement = templateClone.querySelector( '.mini-cards-list__card' );

			// Null check.
			if ( ! cardImageElement || ! cardTitleElement || ! cardDateElement || ! cardElement ) {
				// Bail.
				return;
			}

			// Set the required properties.
			cardImageElement.src = destination.imageUrl;
			cardTitleElement.textContent = destination.label;
			cardDateElement.textContent = month.label;
			cardElement.setAttribute( 'data-destination', destination.value );
			cardElement.setAttribute( 'data-date', month.value );
			cardElement.setAttribute( 'data-parent', destination.parent ?? '' );

			// Append the card.
			this.recentSearchCardsContainer.appendChild( cardElement );

			// Setup event
			cardElement.addEventListener( 'click', () => {
				// Get is active attribute
				const isActive = cardElement.hasAttribute( 'data-is-active' );

				// Is this card active?
				if ( ! isActive ) {
					//Add the destination
					clearAllFilters();
					addDestination( { value: destination.value, label: destination.label, parent: destination.parent } );
					addMonth( { value: month.value, label: month.label } );

					// Add is active
					cardElement.setAttribute( 'data-is-active', '' );
				} else {
					// Remove is active
					cardElement.removeAttribute( 'data-is-active' );
					removeDestination( destination.value );
					removeMonth( month.value );
				}
			} );

			// Subscribe to update the active status for the card.
			subscribe( ( expeditionSearchState: ExpeditionSearchState ) => {
				// Get the state.
				const { destinations, months } = expeditionSearchState;

				// Check if the card's month and destination are in expedition search state.
				if (
					destinations.find( ( dest ) => dest.value === destination.value || dest.value === destination.parent ) &&
					months.find( ( montInState ) => montInState.value === month.value )
				) {
					// yes, set this card as active.
					cardElement.setAttribute( 'data-is-active', '' );
				} else {
					// Remove the active attribute
					cardElement.removeAttribute( 'data-is-active' );
				}
			} );
		} );
	}

	/**
	 * Sets up the store subscription after content is loaded to prevent undefined errors.
	 */
	setupSubscription() {
		// Get the subscribe function.
		const { subscribe: subscribeToFiltersBarStore, getState: getFiltersBarState } = zustand.stores.searchFiltersBar;

		// Subscribe to the store.
		subscribeToFiltersBarStore( this.update.bind( this ) );
		this.update( getFiltersBarState() );
	}
}

// Define the element
customElements.define( 'quark-expedition-search-recent-searches', QuarkExpeditionSearchRecentSearches );
