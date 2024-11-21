/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Internal dependencies.
 */
import { loadMoreResults } from '../actions';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * LoadMore Class.
 */
export class LoadMore extends HTMLElement {
	/**
	 * Properties.
	 */
	private loadMoreButton: HTMLElement | null;
	private loadMoreButtonText: HTMLElement | null;

	/**
	 * Constructor.
	 */
	constructor() {
		// Parent.
		super();

		// Elements.
		this.loadMoreButton = this.querySelector( '.expedition-search__load-more-button' );
		this.loadMoreButtonText = this.querySelector( '.expedition-search__load-more-button > .btn__content-text' );

		// Events.
		this.loadMoreButton?.addEventListener( 'click', loadMoreResults.bind( this ) );

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
		const { hasNextPage, loadMoreResults: loadMoreState, remainingCount, page, loading } = state;

		// If loading, hide the Load more button.
		if ( loading ) {
			this.loadMoreButton?.setAttribute( 'data-hidden', '' );
		} else {
			this.loadMoreButton?.removeAttribute( 'data-hidden' );
		}

		// If next page is available, show the Load more button, else hide it.
		if ( hasNextPage ) {
			this.setAttribute( 'active', 'true' );
		} else {
			this.setAttribute( 'active', 'false' );
		}

		// Set loading state.
		if ( loadMoreState ) {
			// Set loading to true.
			this.setAttribute( 'loading', 'true' );
		} else {
			// Set loading to false.
			this.setAttribute( 'loading', 'false' );
		}

		// Update load more button text.
		if ( this.loadMoreButtonText && loadMoreState ) {
			// Update load more button text with loading text.
			const loadingText = this.getAttribute( 'loading-text' ) ?? '';
			this.loadMoreButtonText.innerText = loadingText;
		} else if ( this.loadMoreButtonText && typeof remainingCount === 'number' ) {
			// Update load more button text with remaining count.
			const loadMoreText = this.getAttribute( 'load-more-text' ) ?? '';
			this.loadMoreButtonText.innerText = `${ loadMoreText } (${ remainingCount })`;

			// Scroll into view.
			if ( page > 1 ) {
				this.scrollToNewCards( page );
			}
		}
	}

	/**
	 * Scroll to new cards.
	 *
	 * @param {number} page New loaded Page.
	 */
	scrollToNewCards( page: number ): void {
		/**
		 * Get card index to focus.
		 *
		 * Page: current page no.
		 * 4: number of cards per page - partials/expedition-search.php.
		 * 1: first card in the new page.
		 *
		 * Example:
		 * page: 1
		 * page - 1: 1 * 8 + 1 = 9
		 *
		 * page: 2
		 * page - 1: 2 * 8 + 1 = 17
		 */
		const focusCardIndex = ( ( page - 1 ) * 8 ) + 1;
		const focusCard = document.querySelector( `.expedition-search__results .expedition-cards__card:nth-child(${ focusCardIndex })` );

		// Validate focus card and scroll into view.
		if ( typeof focusCard === 'object' && focusCard !== null ) {
			focusCard.scrollIntoView();
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-expedition-search-load-more', LoadMore );
