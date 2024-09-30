/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { hideSearchFiltersAction, showSearchFiltersAction } from '../actions';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearchSidebarFiltersHeader Class.
 */
export default class ExpeditionSearchSidebarFiltersHeader extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly hideFiltersBtn: HTMLButtonElement | null;
	private readonly showFiltersBtn: HTMLButtonElement | null;
	private readonly filtersHeaderTitle: HTMLElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Subscribe.
		subscribe( this.update.bind( this ) );

		// Elements.
		this.hideFiltersBtn = this.querySelector( '.expedition-search__sidebar-filters-hide-button' );
		this.showFiltersBtn = this.querySelector( '.expedition-search__sidebar-filters-show-button' );
		this.filtersHeaderTitle = this.querySelector( '.expedition-search__sidebar-filters-header-title' );

		// Events
		this.hideFiltersBtn?.addEventListener( 'click', () => hideSearchFiltersAction() );
		this.showFiltersBtn?.addEventListener( 'click', () => showSearchFiltersAction() );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: ExpeditionSearchState ): void {
		// Get state.
		const { areSearchFiltersShown } = state;

		// Show and hide accordingly.
		if ( areSearchFiltersShown ) {
			this.showFiltersBtn?.setAttribute( 'data-hidden', '' );
			this.hideFiltersBtn?.removeAttribute( 'data-hidden' );
			this.filtersHeaderTitle?.removeAttribute( 'data-hidden' );
		} else {
			this.showFiltersBtn?.removeAttribute( 'data-hidden' );
			this.hideFiltersBtn?.setAttribute( 'data-hidden', '' );
			this.filtersHeaderTitle?.setAttribute( 'data-hidden', '' );
		}
	}
}
