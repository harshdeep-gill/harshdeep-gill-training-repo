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
			travelers,
			noResultsMarkup,
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
			} else if ( markup ) {
				this.resultsContainer.innerHTML = markup;
			} else {
				this.resultsContainer.innerHTML = noResultsMarkup;
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
			this.updateProductOptionCardCabins( cabinClasses );
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

		// Check if traveler options have been selected.
		if ( travelers.length ) {
			this.updateProductOptionCardRoomOptions( travelers );
		} else {
			// Get the cards.
			const allRooms = this.querySelectorAll( '.product-options-cards__room' ) as NodeListOf<HTMLElement>;

			// Check if rooms exist.
			if ( allRooms.length ) {
				// Loop through all rooms.
				allRooms.forEach( ( room ) => {
					// Set all rooms to display flex.
					room.style.display = 'flex';
				} );
			}
		}
	}

	/**
	 * Update Product Option Card Cabins.
	 *
	 * @param {Array} cabinClasses Selected cabin classes.
	 */
	updateProductOptionCardCabins( cabinClasses: ExpeditionSearchFilterState[] ) {
		// Get the cards.
		const productOptionCards = this.querySelectorAll( '.product-options-cards__card' ) as NodeListOf<HTMLElement>;

		// Check if product option cards exist.
		if ( ! productOptionCards?.length ) {
			// Bail.
			return;
		}

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

	/**
	 * Update Product Option Card Room Options.
	 *
	 * @param {Array} travelers Selected travelers.
	 */
	updateProductOptionCardRoomOptions( travelers: ExpeditionSearchFilterState[] ) {
		// Get the cards.
		const allRooms = this.querySelectorAll( '.product-options-cards__room' ) as NodeListOf<HTMLElement>;

		// Check if rooms exist.
		if ( ! allRooms.length ) {
			// Bail.
			return;
		}

		// Get the traveler values.
		const travelerValues = travelers.map( ( traveler ) => traveler.value );

		// Loop through all rooms.
		allRooms.forEach( ( room ) => {
			// Get the room input.
			const roomInput = room.querySelector( 'input' );

			// Get the room mask.
			const roomInputMask = roomInput?.getAttribute( 'mask' ) ?? '';

			// If room input mask exists.
			if ( roomInputMask ) {
				// Check if input mask exists among selected traveler values.
				if ( ! travelerValues.includes( roomInputMask ) ) {
					room.style.display = 'none';
					roomInput?.removeAttribute( 'checked' );
				} else {
					room.style.display = 'flex';
				}
			}
		} );
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-expedition-search-results', ExpeditionSearchResults );
