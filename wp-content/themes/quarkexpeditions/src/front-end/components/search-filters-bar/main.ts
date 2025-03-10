/**
 * Global variables.
 */
const { customElements, HTMLElement, zustand } = window;

/**
 * Internal dependencies.
 */
import { initialize, updateDepartureMonths, updateDestinations, updateHistory } from './actions';
import { MonthsMultiSelect } from '../months-multi-select/main';
import { SearchFilterDestinations } from './destinations';
import { SearchFilterDestinationOption } from './destinations/filter-option';
import { MonthsMultiSelectOption } from '../months-multi-select/months-multi-select-option';
import { TPAccordionItemElement } from '@travelopia/web-components';

/**
 * Get Store.
 */
const { subscribe, getState } = zustand.stores.searchFiltersBar;

/**
 * Class SearchFiltersBar.
 */
export class SearchFiltersBar extends HTMLElement {
	/**
	 * Properties.
	 */
	private searchFiltersModal: HTMLElement | null;
	private searchButton: HTMLElement | null;
	private searchModalButton: HTMLElement | null | undefined;
	private headerSearchButton: HTMLElement | null;
	private searchModalDestinationsButton: HTMLElement | null;
	private searchModalDeparturesButton: HTMLElement | null;
	private clearAllButton: HTMLElement | null;
	private destinationFilters: HTMLElement | null | undefined;
	private departureMonthsFilters: HTMLElement | null | undefined;
	private filtersApiUrl: string | null;
	private searchPageUrl: string | null;
	private destinationSelector: SearchFilterDestinations | null | undefined;
	private departureMonthsSelectors: NodeListOf<MonthsMultiSelect> | null | undefined;
	private defaultDepartureMonthsPlaceholder: string;
	private destinationsAccordion: TPAccordionItemElement | null | undefined;
	private departuresAccordion: TPAccordionItemElement | null | undefined;

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
		this.searchButton = this.querySelector( '.search-filters-bar__search-button' );
		this.searchModalButton = this.searchFiltersModal?.querySelector( '.search-filters-bar__modal-button-search' );
		this.headerSearchButton = document.querySelector( '.header__search-item' );
		this.clearAllButton = document.querySelector( '.search-filters-bar__modal-button-clear-all' );
		this.destinationsAccordion = this.searchFiltersModal?.querySelector( 'tp-accordion-item#search-filters-bar-destinations-accordion' );
		this.departuresAccordion = this.searchFiltersModal?.querySelector( 'tp-accordion-item#search-filters-bar-departures-accordion' );

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
			selector?.addEventListener( 'reset', () => updateDepartureMonths( [] ) );
		} );

		// Search Button.
		this.searchButton?.addEventListener( 'click', this.redirectToSearchPage.bind( this ) );
		this.searchModalButton?.addEventListener( 'click', this.redirectToSearchPage.bind( this ) );
		this.headerSearchButton?.addEventListener( 'click', this.toggleDestinationFilterOptions.bind( this ) );

		// Clear All Button.
		this.clearAllButton?.addEventListener( 'click', this.clearAllFilterSelection.bind( this ) );

		/**
		 * Destinations Accordion Events.
		 */

		// close.
		this.destinationsAccordion?.addEventListener( 'close', () => {
			// Open departures accordion.
			this.departuresAccordion?.setAttribute( 'open', 'yes' );
		} );

		// open.
		this.destinationsAccordion?.addEventListener( 'open', () => {
			// close departures accordion.
			this.departuresAccordion?.removeAttribute( 'open' );
		} );

		/**
		 * Departures Accordion Events.
		 */

		// close.
		this.departuresAccordion?.addEventListener( 'close', () => {
			// Open destinations accordion.
			this.destinationsAccordion?.setAttribute( 'open', 'yes' );
		} );

		// open.
		this.departuresAccordion?.addEventListener( 'open', () => {
			// Close destinations accordion.
			this.destinationsAccordion?.removeAttribute( 'open' );
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
	 * Redirect to the search page URL.
	 */
	redirectToSearchPage() {
		// Get state.
		const { searchPageUrl } = getState();

		// Update the history.
		updateHistory();

		// Redirect to Search URL from state.
		if ( searchPageUrl ) {
			window.location.href = searchPageUrl;
		}
	}

	/**
	 * Initialize component.
	 */
	initialize() {
		// Initialize data for the component.
		initialize(
			{
				filtersApiUrl: this.filtersApiUrl,
				searchPageUrl: this.searchPageUrl,
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
		// Null check.
		if ( ! this.destinationSelector || ! this.searchFiltersModal ) {
			// Bail
			return;
		}

		// Get the selected value.
		const values = Array.from( this.destinationSelector.value );

		// Check if we have an empty array
		if ( values.length === 0 ) {
			// Bail.
			return;
		}

		// Get the attribute selector based on the selected values.
		const valueAttributeSelector = values.map( ( value ) => `[value="${ value }"]` ).join( ',' );

		// Get the selected options.
		const selectedOptions: NodeListOf<SearchFilterDestinationOption> = this.searchFiltersModal.querySelectorAll( `quark-search-filters-bar-destinations-option${ valueAttributeSelector }` );

		// Initialize destinations.
		const destinations: SearchFiltersBarDestinationState[] = [];

		// Initialize search field label.
		let fieldLabel = '';

		// Loop through the selected filters.
		selectedOptions.forEach( ( selectedOption ) => {
			// Get the attributes.
			const value = selectedOption.getAttribute( 'value' ) ?? '';
			const label = selectedOption.getAttribute( 'label' ) ?? '';
			const imageUrl = selectedOption.getAttribute( 'image-url' ) ?? '';
			const parent = selectedOption.getAttribute( 'parent' ) ?? '';

			// Empty checks
			if ( ! ( value && label ) ) {
				// Bail
				return;
			}

			// Check and set field label.
			if ( '' === fieldLabel ) {
				fieldLabel = label;
			}

			// Add the destination.
			destinations.push( { value, label, imageUrl, parent } );
		} );

		// Update the destinations
		updateDestinations( destinations );

		// Update the destinations field label.
		this.updateDestinationsPlaceholder( fieldLabel );

		// Activate departure filter.
		this.toggleDepartureFilterOptions();
	}

	/**
	 * Update selected departure months state.
	 *
	 * @param {Event} event event.
	 */
	updateDepartureMonthsState( event: Event ) {
		// Null check.
		if ( ! event.target || ! this.searchFiltersModal ) {
			// Bail.
			return;
		}

		// Get the current selector.
		const currentSelector = event.target as MonthsMultiSelect;

		// Get the values.
		const values = Array.from( currentSelector.value );

		// Check if we have an empty array
		if ( values.length === 0 ) {
			// Bail.
			this.updateMonthsPlaceholder( this.defaultDepartureMonthsPlaceholder );
			updateDepartureMonths( [] );

			// Bail.
			return;
		}

		// Get the attribute selector based on the selected values.
		const valueAttributeSelector = values.map( ( value ) => `[value="${ value }"]` ).join( ',' );

		// Get the selected options
		const selectedOptions: NodeListOf<MonthsMultiSelectOption> = this.searchFiltersModal?.querySelectorAll( `quark-months-multi-select-option${ valueAttributeSelector }` );

		// Initialize months
		const months: SearchFiltersBarMonthState[] = [];

		// Initialize field label.
		let fieldLabel = '';

		// Loop through the selected filters.
		selectedOptions.forEach( ( selectedOption ) => {
			// Get the attributes
			const value = selectedOption.getAttribute( 'value' ) ?? '';
			const label = selectedOption.getAttribute( 'label' ) ?? '';

			// Empty checks
			if ( ! ( value && label ) ) {
				// Bail.
				return;
			}

			// Check and set field label.
			if ( '' === fieldLabel ) {
				fieldLabel = label;
			}

			// Add the month
			months.push( { value, label } );
		} );

		// Update the months
		updateDepartureMonths( months );

		// Update the months filter label
		this.updateMonthsPlaceholder( fieldLabel );
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
		// Get the months placeholder.
		const monthsPlaceholder = this.searchFiltersModal?.querySelector( '.search-filters-bar__departure-months-placeholder' );

		// Update the placeholder label.
		if ( monthsPlaceholder ) {
			monthsPlaceholder.innerHTML = label ? label : this.defaultDepartureMonthsPlaceholder;
		}
	}

	/**
	 * Clear all filter selection.
	 */
	clearAllFilterSelection() {
		// Loop through all month selectors.
		this.departureMonthsSelectors?.forEach( ( selector ) => {
			// Reset the selector.
			selector?.resetSelector();
		} );

		// Unselect all destination options.
		if ( this.destinationSelector ) {
			this.destinationSelector.unSelectAll();
		}
	}
}

// Define Element.
customElements.define( 'quark-search-filters-bar', SearchFiltersBar );
