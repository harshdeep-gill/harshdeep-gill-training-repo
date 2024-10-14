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
	private readonly filtersCountElements: NodeListOf<HTMLElement>;

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
		this.filtersCountElements = this.querySelectorAll( '.expedition-search__filter-count' );

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
		const {
			areSearchFiltersShown,
			destinations,
			ships,
			adventureOptions,
			languages,
			expeditions,
			cabinClasses,
			travelers,
		} = state;

		// Null check.
		if ( this.filtersCountElements.length ) {
			const filtersCount = destinations.length +
				ships.length +
				adventureOptions.length +
				languages.length +
				expeditions.length +
				cabinClasses.length +
				travelers.length;

			// Initialize.
			let countString = '';

			// Check and update count.
			if ( filtersCount > 0 ) {
				countString = `(${ filtersCount })`;
			}

			// Set the inner html.
			this.filtersCountElements.forEach( ( countElement ) => countElement.innerHTML = countString );
		}

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
