/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.bookDeparturesExpeditions;

/**
 * Internal dependencies.
 */
import { markupUpdated } from '../actions';

/**
 * BookDeparturesExpeditionsResults Class.
 */
export default class BookDeparturesExpeditionsResults extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
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
		this.selector = this.dataset?.selector ?? '';
		this.resultsContainer = this.querySelector( this.selector );

		// Subscribe.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: BookDeparturesExpeditionsState ): void {
		// Update results.
		this.updateResults( state );
	}

	/**
	 * Update results.
	 *
	 * @param {Object} state State.
	 */
	updateResults( state: BookDeparturesExpeditionsState ) {
		// Get state.
		const {
			updateMarkup,
			resetMarkup,
			markup,
			loadMoreResults,
		} = state;

		// If product cards element not available, return.
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
	}
}

/**
 * Initialize.
 */
customElements.define( 'quark-book-departures-expeditions-results', BookDeparturesExpeditionsResults );
