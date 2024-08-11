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
const { subscribe } = zustand.stores.bookDeparturesExpeditions;

/**
 * LoadMore Class.
 */
export class LoadMore extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
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
		this.loadMoreButton = this.querySelector( '.book-departures-expeditions__load-more-button' );
		this.loadMoreButtonText = this.querySelector( '.book-departures-expeditions__load-more-button > .btn__content-text' );
	}

	/**
	 * Connected callback.
	 */
	connectedCallback() {
		// Events.
		this.loadMoreButton?.addEventListener( 'click', () => loadMoreResults() );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: BookDeparturesExpeditionsState ): void {
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
			this.loadMoreButtonText.innerText = `View more departures (${ remainingCount })`;
		}
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-book-departures-expeditions-load-more', LoadMore );
