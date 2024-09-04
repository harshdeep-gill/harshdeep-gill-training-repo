/**
 * Global variables
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies.
 */
import { updateFilters } from '../actions';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Filters controller class.
 */
export default class DatesRatesFiltersControllerElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly regionSeasonFilter: NodeListOf<HTMLInputElement>;
	private readonly expeditionsFilter: NodeListOf<HTMLInputElement>;
	private readonly adventureOptionsFilter: NodeListOf<HTMLInputElement>;
	private readonly departureMonthsFilter: NodeListOf<HTMLInputElement>;
	private readonly durationsFilter: NodeListOf<HTMLInputElement>;
	private readonly shipsFilter: NodeListOf<HTMLInputElement>;
	private readonly applyFiltersButton: HTMLButtonElement | null;
	private readonly clearAllButton: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize parent properties.
		super();

		/**
		 * Initialize filters.
		 *
		 * These need to be queried on the document because these
		 * are inside a drawer which is pulled out of the parent on initialization.
		 */
		this.regionSeasonFilter = document.querySelectorAll( '#filters-accordion-seasons input[type="checkbox"]' );
		this.expeditionsFilter = document.querySelectorAll( '#filters-accordion-expeditions input[type="checkbox"]' );
		this.adventureOptionsFilter = document.querySelectorAll( '#filters-accordion-adventure-options input[type="checkbox"]' );
		this.departureMonthsFilter = document.querySelectorAll( '#filters-accordion-months input[type="checkbox"]' );
		this.durationsFilter = document.querySelectorAll( '#filters-accordion-durations input[type="checkbox"]' );
		this.shipsFilter = document.querySelectorAll( '#filters-accordion-ships input[type="checkbox"]' );
		this.applyFiltersButton = document.querySelector( '.dates-rates__apply-filters-btn' );
		this.clearAllButton = document.querySelector( '.dates-rates__cta-clear-filters' );

		// Add Event Listeners
		this.applyFiltersButton?.addEventListener( 'click', this.handleApplyFilters.bind( this ) );
		this.clearAllButton?.addEventListener( 'click', this.handleClearAll.bind( this ) );

		// Subscribe to the store and synchronize the currency inputs.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state
	 */
	update( state: DatesRatesState ) {
		// Get the selected filters.
		const { selectedFilters } = state;

		// Get the currency value.
		const {
			seasons,
			adventure_options: adventureOptions,
			durations,
			expeditions,
			months,
			ships,
		} = selectedFilters;

		// Update the checkbox according to the state.
		this.updateCheckboxes( this.regionSeasonFilter, seasons );
		this.updateCheckboxes( this.adventureOptionsFilter, adventureOptions );
		this.updateCheckboxes( this.durationsFilter, durations );
		this.updateCheckboxes( this.expeditionsFilter, expeditions );
		this.updateCheckboxes( this.departureMonthsFilter, months );
		this.updateCheckboxes( this.shipsFilter, ships );
	}

	/**
	 * Handles the click on `Apply Filters` button.
	 */
	handleApplyFilters() {
		// Update the state.
		updateFilters( {
			seasons: this.getSelectedFilterValues( this.regionSeasonFilter ),
			expeditions: this.getSelectedFilterValues( this.expeditionsFilter ),
			adventure_options: this.getSelectedFilterValues( this.adventureOptionsFilter ),
			months: this.getSelectedFilterValues( this.departureMonthsFilter ),
			durations: this.getSelectedFilterValues( this.durationsFilter ),
			ships: this.getSelectedFilterValues( this.shipsFilter ),
		} );
	}

	/**
	 * Returns the selected values from a list of filters.
	 *
	 * @param {HTMLInputElement} filterList
	 *
	 * @return {string[]} selectedFilters
	 */
	getSelectedFilterValues( filterList: NodeListOf<HTMLInputElement> ) {
		// Initialize the selectedFilters.
		const selectedFilters: string[] = [];

		// Loop through the inputs.
		filterList.forEach( ( checkboxInput ) => {
			// Check the value.
			if ( checkboxInput.checked ) {
				// Push to selected values.
				selectedFilters.push( checkboxInput.value );
			}
		} );

		// Return the selectedFilters.
		return selectedFilters;
	}

	/**
	 * Updates all the checkboxes for the associated filter list.
	 *
	 * @param {NodeList} filterList     The filter list.
	 * @param {string[]} selectedValues The selected values.
	 */
	updateCheckboxes( filterList: NodeListOf<HTMLInputElement>, selectedValues: string[] = [] ) {
		// Clear the checkboxes.
		filterList.forEach( ( checkboxInput ) => {
			// Check if the value of the checkbox is in the selected values.
			if ( ! selectedValues.includes( checkboxInput.value ) ) {
				checkboxInput.checked = false;
			} else {
				checkboxInput.checked = true;
			}
		} );
	}

	/**
	 * Handle the click on Clear All button.
	 */
	handleClearAll() {
		// Update the filters.
		updateFilters( {
			seasons: [],
			expeditions: [],
			adventure_options: [],
			durations: [],
			months: [],
			ships: [],
			currency: 'USD',
		} );
	}
}
