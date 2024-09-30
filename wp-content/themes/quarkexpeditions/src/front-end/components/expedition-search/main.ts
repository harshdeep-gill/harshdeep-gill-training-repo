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
	private shipId: number | undefined;

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
	}

	/**
	 * Connected Callback.
	 */
	connectedCallback() {
		// Set settings data.
		this.setSettingsData();

		// Initialize.
		this.initialize();
	}

	/**
	 * Initialize component.
	 */
	initialize() {
		// Initialize data for the component.
		initialize( {
			partial: this.partial,
			selector: this.selector,
			shipId: this.shipId,
		} );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: ExpeditionSearchState ): void {
		// Get state.
		const { loading, resultCount } = state;

		// Set loading state.
		if ( loading ) {
			// Set loading to true.
			this.setAttribute( 'loading', 'true' );
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
		this.shipId = Number( this.resultsContainer.getAttribute( 'ship-id' ) ) ?? 0;
	}
}

// Define element.
customElements.define( 'quark-expedition-search', ExpeditionSearch );
