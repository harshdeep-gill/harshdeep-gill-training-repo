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

		// Update load more button text with remaining count.
		if ( this.loadMoreButtonText && typeof remainingCount === 'number' ) {
			const loadMoreText = this.getAttribute( 'load-more-text' ) ?? '';
			this.loadMoreButtonText.innerText = `${ loadMoreText } (${ remainingCount })`;
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-expedition-search-load-more', LoadMore );
