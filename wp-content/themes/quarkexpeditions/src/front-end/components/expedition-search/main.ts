/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Internal dependencies.
 */
import { initialize } from './actions';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearch Class.
 */
export class ExpeditionSearch extends HTMLElement {
	/**
	 * Properties.
	 */
	private resultsContainer: HTMLElement | null;
	private resultCountValue: HTMLElement | null;
	private partial: string | undefined;
	private selector: string | undefined;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Subscribe.
		subscribe( this.update.bind( this ) );

		// Elements.
		this.resultsContainer = this.querySelector( '.expedition-search__results' );
		this.resultCountValue = this.querySelector( '.expedition-search__result-count-value' );

		// Set on load event to make sure all the children are updated about the state.
		window.addEventListener( 'load', this.initialize.bind( this ) );
	}

	/**
	 * Connected Callback.
	 */
	connectedCallback() {
		// Set settings data.
		this.setSettingsData();
	}

	/**
	 * Initialize component.
	 */
	initialize() {
		// Null check
		if ( ! this.resultsContainer ) {
			// Bail.
			return;
		}

		// Get the server rendered values if available
		let isServerRendered = this.resultsContainer.getAttribute( 'server-rendered' ) === 'yes';
		const serverRenderData = {
			resultsCount: Number.NaN,
			remainingCount: Number.NaN,
			page: Number.NaN,
			nextPage: Number.NaN,
		};

		// Is it server rendered?
		if ( isServerRendered && serverRenderData ) {
			serverRenderData.resultsCount = parseInt( this.resultsContainer.getAttribute( 'results-count' ) ?? '' );
			serverRenderData.remainingCount = parseInt( this.resultsContainer.getAttribute( 'remaining-count' ) ?? '' );
			serverRenderData.page = parseInt( this.resultsContainer.getAttribute( 'page' ) ?? '' );
			serverRenderData.nextPage = parseInt( this.resultsContainer.getAttribute( 'next-page' ) ?? '' );

			// Check if we have valid values.
			isServerRendered = ! (
				Number.isNaN( serverRenderData.remainingCount ) ||
				Number.isNaN( serverRenderData.resultsCount ) ||
				Number.isNaN( serverRenderData.page ) ||
				Number.isNaN( serverRenderData.nextPage )
			);
		}

		// Initialize data for the component.
		initialize( {
			partial: this.partial,
			selector: this.selector,
			serverRenderData: isServerRendered ? serverRenderData : undefined,
		} );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: ExpeditionSearchState ): void {
		// Get state.
		const { loading, resultCount, loadMoreResults } = state;

		// Set loading state.
		if ( loading ) {
			// Set loading to true.
			this.setAttribute( 'loading', 'true' );

			// Check if we loaded more results?
			if ( ! loadMoreResults ) {
				this.scrollIntoView();
			}
		} else {
			// Set loading to false.
			this.setAttribute( 'loading', 'false' );
		}

		// Set results count.
		if ( this.resultCountValue && typeof resultCount === 'number' ) {
			// Update the result count.
			this.resultCountValue.innerText = resultCount?.toString();
		}
	}

	/**
	 * Set settings data.
	 */
	setSettingsData(): void {
		// Check if resultsContainer exists.
		if ( ! this.resultsContainer ) {
			// Bail early.
			return;
		}

		// Set all settings data.
		this.partial = this.resultsContainer.getAttribute( 'partial' ) ?? 'expedition-search';
		this.selector = this.resultsContainer.getAttribute( 'selector' ) ?? '';
	}
}

// Define element.
customElements.define( 'quark-expedition-search', ExpeditionSearch );
