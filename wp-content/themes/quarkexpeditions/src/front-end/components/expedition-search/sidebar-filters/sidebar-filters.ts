/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * External dependencies
 */
import { slideElementDown, slideElementUp } from '../../../global/utility';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearch Class.
 */
export default class ExpeditionSearchSidebarFilters extends HTMLElement {
	/**
	 * Properties.
	 */
	private filtersContent: HTMLElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Subscribe.
		subscribe( this.update.bind( this ) );

		// Elements.
		this.filtersContent = null;
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: ExpeditionSearchState ): void {
		// Get the element.
		this.filtersContent = this.querySelector( '.expedition-search__sidebar-filters-content' );

		// Null check.
		if ( ! this.filtersContent ) {
			// Bail.
			return;
		}

		// Get state.
		const { areSearchFiltersShown } = state;

		// Set loading state.
		if ( areSearchFiltersShown ) {
			// Set loading to true.
			slideElementDown( this.filtersContent, 300 );
		} else {
			// Set loading to false.
			slideElementUp( this.filtersContent, 300 );
		}
	}
}
