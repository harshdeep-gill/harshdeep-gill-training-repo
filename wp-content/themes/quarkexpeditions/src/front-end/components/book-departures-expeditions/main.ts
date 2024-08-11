/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * External dependencies.
 */
import { TPMultiSelectElement } from '@travelopia/web-components';

/**
 * Internal dependencies.
 */
import { initialize, updateCurrency, updateSort } from './actions';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.bookDeparturesExpeditions;

/**
 * BookDeparturesExpeditions Class.
 */
export class BookDeparturesExpeditions extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private currencyDropdown: TPMultiSelectElement | null;
	private sortDropdown: TPMultiSelectElement | null;
	private resultsContainer: HTMLElement | null;
	private resultsCountElement: HTMLElement | null;
	private partial: string | undefined;
	private selector: string | undefined;
	private expeditionId: number | undefined;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Subscribe.
		subscribe( this.update.bind( this ) );

		// Elements.
		this.currencyDropdown = this.querySelector( '.book-departures-expeditions__filters-currency > tp-multi-select' );
		this.sortDropdown = this.querySelector( '.book-departures-expeditions__filters-sort > tp-multi-select' );
		this.resultsContainer = this.querySelector( '.book-departures-expeditions__results' );
		this.resultsCountElement = this.querySelector( '.book-departures-expeditions__result-count' );

		// Set settings data.
		this.setSettingsData();

		// Initialize.
		this.initialize();
	}

	/**
	 * Connected Callback.
	 */
	connectedCallback() {
		// Events.
		this.currencyDropdown?.addEventListener( 'change', this.updateCurrencyState.bind( this ) );
		this.sortDropdown?.addEventListener( 'change', this.updateSortState.bind( this ) );
	}

	/**
	 * Initialize component.
	 */
	initialize() {
		// Initialize data for the component.
		initialize( {
			partial: this.partial,
			selector: this.selector,
			expeditionId: this.expeditionId,
		} );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: BookDeparturesExpeditionsState ): void {
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
		if ( this.resultsCountElement && typeof resultCount === 'number' ) {
			// Update the result count.
			this.resultsCountElement.innerText = `Showing ${ resultCount } departures`;
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

		// Get the settings data from dataset.
		const settingsData = this.resultsContainer?.dataset;

		// Early return.
		if ( ! settingsData ) {
			// Bail, if no data.
			return;
		}

		// Set all settings data.
		this.partial = settingsData?.partial;
		this.selector = settingsData?.selector;
		this.expeditionId = Number( settingsData?.expeditionId );
	}

	/**
	 * Update Currency Value.
	 *
	 * @param {string[]} currency Currency.
	 */
	updateCurrency( currency: string[] ) {
		// Check if currency and currencyDropdown are present.
		if ( currency && this.currencyDropdown ) {
			// Set the value of the currency selector.
			this.currencyDropdown.value = currency;
		}
	}

	/**
	 * Update Sort Value.
	 *
	 * @param {string[]} sort Sort Type.
	 */
	updateSort( sort: string[] ) {
		// Check if sort and sortDropdown are present.
		if ( sort && this.sortDropdown ) {
			// Set the value of the sort selector.
			this.sortDropdown.value = sort;
		}
	}

	/**
	 * Update currency state.
	 */
	updateCurrencyState() {
		// If currencyDropdown not available, return.
		if ( ! this.currencyDropdown ) {
			// Early return.
			return;
		}

		// Get currency if set, else set to default 'USD' value.
		const currency: string = this.currencyDropdown?.value[ 0 ] ?? 'USD';

		// Update currency.
		updateCurrency( currency );
	}

	/**
	 * Update sort state.
	 */
	updateSortState() {
		// If sortDropdown not available, return.
		if ( ! this.sortDropdown ) {
			// Early return.
			return;
		}

		// Get sort if set, else set to default 'date-now' value.
		const sort: string = this.sortDropdown?.value[ 0 ] ?? 'date-now';

		// Update sort.
		updateSort( sort );
	}
}

// Define element.
customElements.define( 'quark-book-departures-expeditions', BookDeparturesExpeditions );
