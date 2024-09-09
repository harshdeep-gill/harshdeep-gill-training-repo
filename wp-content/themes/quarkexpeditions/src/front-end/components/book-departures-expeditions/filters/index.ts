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
import { updateSort } from '../actions';
import { setCurrency } from '../../../global/store/actions';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.bookDeparturesExpeditions;

/**
 * BookDeparturesExpeditions Class.
 */
export class BookDeparturesExpeditionsFilters extends HTMLElement {
	/**
	 * Properties.
	 */
	private currencyDropdown: TPMultiSelectElement | null;
	private sortDropdown: TPMultiSelectElement | null;

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

		// Events.
		this.currencyDropdown?.addEventListener( 'change', this.updateCurrencyState.bind( this ) );
		this.sortDropdown?.addEventListener( 'change', this.updateSortState.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: BookDeparturesExpeditionsState ): void {
		// Get state.
		const { loading } = state;

		// Set loading state.
		if ( loading ) {
			// Set loading to true.
			this.setAttribute( 'loading', 'true' );
		} else {
			// Set loading to false.
			this.setAttribute( 'loading', 'false' );
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
		setCurrency( currency );
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
customElements.define( 'quark-book-departures-expeditions-filters', BookDeparturesExpeditionsFilters );
