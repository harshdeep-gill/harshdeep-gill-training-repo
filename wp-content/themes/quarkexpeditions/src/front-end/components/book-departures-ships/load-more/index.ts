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
const { subscribe } = zustand.stores.bookDeparturesShips;

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

		// Subscribe.
		subscribe( this.update.bind( this ) );

		// Elements.
		this.loadMoreButton = this.querySelector( '.book-departures-ships__load-more-button' );
		this.loadMoreButtonText = this.querySelector( '.book-departures-ships__load-more-button > .btn__content-text' );

		// Events.
		this.loadMoreButton?.addEventListener( 'click', loadMoreResults.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: BookDeparturesShipsState ): void {
		// Get state.
		const { hasNextPage, loadMoreResults: loadMoreState, remainingCount } = state;

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
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-book-departures-ships-load-more', LoadMore );
