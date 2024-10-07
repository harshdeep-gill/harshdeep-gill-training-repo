/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Internal dependencies.
 */
import { initialize, updateDepartureMonths, updateDestinations } from './actions';
import { MonthsMultiSelect } from '../months-multi-select/main';
import { SearchFilterDestinations } from './destinations';
import { SearchFilterDestinationOption } from './destinations/filter-option';
import { MonthsMultiSelectOption } from '../months-multi-select/months-multi-select-option';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.searchFiltersBar;

/**
 * Class SearchFiltersBar.
 */
export class SearchFiltersBar extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchFiltersModal: HTMLElement | null;
	private searchModalDestinationsButton: HTMLElement | null;
	private searchModalDeparturesButton: HTMLElement | null;
	private destinationFilters: HTMLElement | null | undefined;
	private departureMonthsFilters: HTMLElement | null | undefined;
	private filtersApiUrl: string | null;
	private searchPageUrl: string | null;
	private destinationSelector: SearchFilterDestinations | null | undefined;
	private departureMonthsSelectors: NodeListOf<MonthsMultiSelect> | null | undefined;
	private defaultDepartureMonthsPlaceholder: string;

	/**
	 * Constructor.
	 */
	constructor() {
		// Initialize super.
		super();

		// Subscribe.
		subscribe( this.update.bind( this ) );

		// Settings.
		this.filtersApiUrl = this.getAttribute( 'filters-api-url' );
		this.searchPageUrl = this.getAttribute( 'search-page-url' );

		// Elements.
		this.searchFiltersModal = document.querySelector( '.search-filters-bar__modal' );
		this.searchModalDestinationsButton = this?.querySelector( '.search-filters-bar__modal-open-button-destinations' );
		this.searchModalDeparturesButton = this?.querySelector( '.search-filters-bar__modal-open-button-departures' );
		this.destinationFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__destinations-filter-options' );
		this.departureMonthsFilters = this.searchFiltersModal?.querySelector( '.search-filters-bar__departure-months-filter-options' );
		this.destinationSelector = this.searchFiltersModal?.querySelector( 'quark-search-filters-bar-destinations' );
		this.departureMonthsSelectors = this.searchFiltersModal?.querySelectorAll( 'quark-months-multi-select' );
		this.defaultDepartureMonthsPlaceholder = this.departureMonthsFilters?.getAttribute( 'default-placeholder' ) as string;

		// Event Listeners.
		this.searchModalDestinationsButton?.addEventListener(
			'click',
			this.toggleDestinationFilterOptions.bind( this )
		);
		this.searchModalDeparturesButton?.addEventListener(
			'click',
			this.toggleDepartureFilterOptions.bind( this )
		);
		this.destinationSelector?.addEventListener( 'change', this.updateDestinationsState.bind( this ) );

		// Loop through all month selectors.
		this.departureMonthsSelectors?.forEach( ( selector ) => {
			// Add event listeners.
			selector?.addEventListener( 'change', this.updateDepartureMonthsState.bind( this ) );
			selector?.addEventListener( 'reset', this.updateMonthsPlaceholder.bind( this, this.defaultDepartureMonthsPlaceholder ) );
		} );
	}

	/**
	 * Connected Callback.
	 */
	connectedCallback() {
		// Initialize.
		this.initialize();
	}

	/**
	 * Initialize component.
	 */
	initialize() {
		// Initialize data for the component.
		initialize(
			{
				filtersApiUrl: this.filtersApiUrl,
			}
		);
	}

	/**
	 * Update component.
	 *
	 */
	update(): void {
		// Get state.
	}

	/**
	 * Update selected destinations state.
	 */
	updateDestinationsState() {
		// Get the selected value.
		const value = this.destinationSelector?.value;
		let label = '';

		// Get the selected option.
		const selectedOption: SearchFilterDestinationOption | null | undefined = this.searchFiltersModal?.querySelector( `quark-search-filters-bar-destinations-option[value="${ value?.values().next().value }"]` );

		// Get the label.
		if ( selectedOption ) {
			label = selectedOption?.getAttribute( 'label' ) ?? '';
		}

		// Update destinations state.
		if ( value ) {
			updateDestinations( value );

			// Update the label.
			if ( label ) {
				this.updateDestinationsPlaceholder( label );
			}
		}

		// Activate departure filter.
		this.toggleDepartureFilterOptions();
	}

	/**
	 * Update selected departure months state.
	 *
	 * @param {Event} event event.
	 */
	updateDepartureMonthsState( event: Event ) {
		// Get the current selector.
		const currentSelector = event.target as MonthsMultiSelect;

		// Get the selected value.
		const value = currentSelector?.value;
		let label = '';

		// Get the selected option.
		const selectedOption: MonthsMultiSelectOption | null | undefined = this.searchFiltersModal?.querySelector( `quark-months-multi-select-option[value="${ value?.values().next().value }"]` );

		// Get the label.
		if ( selectedOption ) {
			label = selectedOption?.getAttribute( 'label' ) ?? '';
		}

		// Update months state.
		if ( value ) {
			updateDepartureMonths( value );

			// Update placeholder with label.
			if ( label ) {
				this.updateMonthsPlaceholder( label );
			}
		}

		// Activate destinations filter.
		this.toggleDestinationFilterOptions();
	}

	/**
	 * Toggle destination filter options.
	 */
	toggleDestinationFilterOptions() {
		// Check if the elements exist.
		if ( ! this.destinationFilters || ! this.departureMonthsFilters ) {
			// Bail early.
			return;
		}

		// Toggle the filter active state.
		this.destinationFilters?.setAttribute( 'active', 'true' );
		this.departureMonthsFilters?.setAttribute( 'active', 'false' );
	}

	/**
	 * Toggle departure filter options.
	 */
	toggleDepartureFilterOptions() {
		// Check if the elements exist.
		if ( ! this.destinationFilters || ! this.departureMonthsFilters ) {
			// Bail early.
			return;
		}

		// Toggle the filter active state.
		this.departureMonthsFilters?.setAttribute( 'active', 'true' );
		this.destinationFilters?.setAttribute( 'active', 'false' );
	}

	/**
	 * Update destinations placeholder.
	 *
	 * @param {string} label Label.
	 */
	updateDestinationsPlaceholder( label: string ) {
		// Check if label exists.
		if ( ! label ) {
			// Bail.
			return;
		}

		// Get the destinations placeholder.
		const destinationsPlaceholder = this.searchFiltersModal?.querySelector( '.search-filters-bar__destinations-placeholder' );

		// Update the placeholder label.
		if ( destinationsPlaceholder ) {
			destinationsPlaceholder.innerHTML = label;
		}
	}

	/**
	 * Update departure months placeholder.
	 *
	 * @param {string} label Label.
	 */
	updateMonthsPlaceholder( label: string ) {
		// Check if label exists.
		if ( ! label ) {
			// Bail.
			return;
		}

		// Get the months placeholder.
		const monthsPlaceholder = this.searchFiltersModal?.querySelector( '.search-filters-bar__departure-months-placeholder' );

		// Update the placeholder label.
		if ( monthsPlaceholder ) {
			monthsPlaceholder.innerHTML = label ?? '';
		}
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar', SearchFiltersBar );
