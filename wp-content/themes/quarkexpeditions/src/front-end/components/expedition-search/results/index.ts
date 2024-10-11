/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * Internal dependencies.
 */
import { markupUpdated } from '../actions';

/**
 * ExpeditionSearchResults Class.
 */
export default class ExpeditionSearchResults extends HTMLElement {
	/**
	 * Properties.
	 */
	private resultsContainer: HTMLElement | null;
	private selector: string;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize parent.
		super();

		// Element.
		this.selector = this.getAttribute( 'selector' ) ?? '.expedition-search__results';
		this.resultsContainer = this.querySelector( this.selector );

		// Subscribe.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: ExpeditionSearchState ): void {
		// Get state.
		const {
			updateMarkup,
			resetMarkup,
			markup,
			loadMoreResults,
			cabinClasses,
		} = state;

		// If results container element not available, return.
		if ( ! this.resultsContainer ) {
			// Return.
			return;
		}

		// If it's initialized during initial render.
		if ( updateMarkup ) {
			// Update markup.
			if ( loadMoreResults ) {
				this.resultsContainer.innerHTML += markup;
			} else {
				this.resultsContainer.innerHTML = markup;
			}

			// Call markupUpdated for handing actions after markup is updated.
			markupUpdated();
		}

		// Reset the markup to empty, if resetMarkup is true.
		if ( resetMarkup ) {
			this.resultsContainer.innerHTML = '';
		}

		// Check if cabin classes have been selected.
		if ( cabinClasses.length ) {
			const productOptionCards = this.querySelectorAll( '.product-options-cards__card' ) as NodeListOf<HTMLElement>;

			// Check if product option cards exist.
			if ( productOptionCards?.length ) {
				// Get the cabin values.
				const cabinValues = cabinClasses.map( ( cabin ) => cabin.value );

				// Loop through the product option cards.
				productOptionCards.forEach( ( productOptionCard ) => {
					// Get the cabin type.
					const cabinType = productOptionCard.getAttribute( 'type' );

					// If cabin type exists.
					if ( cabinType ) {
						// Check if cabin type exists in selected cabin class values.
						if ( ! cabinValues.includes( cabinType ) ) {
							productOptionCard.style.display = 'none';
						} else {
							productOptionCard.style.display = 'block';
						}
					}
				} );
			}
		} else {
			const productOptionCards = this.querySelectorAll( '.product-options-cards__card' ) as NodeListOf<HTMLElement>;

			// Check if product option cards exist.
			if ( productOptionCards?.length ) {
				// Loop through the product option cards.
				productOptionCards.forEach( ( productOptionCard ) => {
					// Set all cards display block.
					productOptionCard.style.display = 'block';
				} );
			}
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-expedition-search-results', ExpeditionSearchResults );
