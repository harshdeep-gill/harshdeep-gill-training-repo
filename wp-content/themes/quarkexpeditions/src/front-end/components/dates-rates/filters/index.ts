
/**
 * Global variables
 */
const { HTMLElement, zustand } = window;

/**
 * External dependencies.
 */
import { TPMultiSelectElement } from "@travelopia/web-components";

/**
 * Internal dependencies.
 */
import { updateCurrency, updateFilters } from "../actions";

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
	private readonly currencyDropdown: TPMultiSelectElement | null;
	private readonly currencyFilter: NodeListOf<HTMLInputElement>;
	/**
	 * This is to prevent stack overflow from recursive event listener calls.
	 */
	private areFiltersSyncing: boolean;
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
		this.regionSeasonFilter = document.querySelectorAll( '#filters-accordion-region-season input[type="checkbox"]' );
		this.expeditionsFilter = document.querySelectorAll( '#filters-accordion-expedition input[type="checkbox"]' );
		this.adventureOptionsFilter = document.querySelectorAll( '#filters-accordion-adventure-options input[type="checkbox"]' );
		this.departureMonthsFilter = document.querySelectorAll( '#filters-accordion-departure-month input[type="checkbox"]' );
		this.durationsFilter = document.querySelectorAll( '#filters-accordion-duration input[type="checkbox"]' );
		this.shipsFilter = document.querySelectorAll( '#filters-accordion-ship input[type="checkbox"]' );
		this.currencyDropdown = document.querySelector( '.dates-rates__filter-currency tp-multi-select' );
		this.currencyFilter = document.querySelectorAll( '#dates-rates-filters-currency input[type="radio"]' );
		this.applyFiltersButton = document.querySelector( '.dates-rates__apply-filters-btn' );
		this.clearAllButton = document.querySelector( '.dates-rates__cta-clear-filters' );
		this.areFiltersSyncing = false;

		// Add Event Listeners
		this.currencyDropdown?.addEventListener( 'change', this.currencyDropdownUpdate.bind( this ) );
		this.currencyFilter.forEach( currencyRadioInput => currencyRadioInput.addEventListener( 'change', this.currencyRadioUpdate.bind( this ) ) );
		this.applyFiltersButton?.addEventListener( 'click', this.handleApplyFilters.bind( this ) );
		this.clearAllButton?.addEventListener( 'click', this.handleClearAll.bind( this ) );

		// Subscribe to the store and synchronize the currency inputs.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param { DatesRatesState } state
	 */
	update( state: DatesRatesState ) {
		// Get the selected filters.
		const { selectedFilters } = state;

		// Set the flag.
		this.areFiltersSyncing = true;

		// Get the currency value.
		const {
			currency,
			seasons,
			adventure_options: adventureOptions,
			durations,
			expeditions,
			months,
			ships,
		} = selectedFilters;

		// Check and update currency inputs.
		if ( currency ) {
			// Standard null check.
			if ( this.currencyDropdown ) {
				// Select the appropriate value.
				this.currencyDropdown.select( currency );
			}

			// Loop through the radio inputs.
			this.currencyFilter.forEach( radioInput => {
				// Check for proper value.
				if ( radioInput.value !== currency ) {
					return;
				}

				// Set the radio to checked.
				radioInput.checked = true;
			} );
		}

		this.updateCheckboxes( this.regionSeasonFilter, seasons );
		this.updateCheckboxes( this.adventureOptionsFilter, adventureOptions );
		this.updateCheckboxes( this.durationsFilter, durations );
		this.updateCheckboxes( this.expeditionsFilter, expeditions );
		this.updateCheckboxes( this.departureMonthsFilter, months );
		this.updateCheckboxes( this.shipsFilter, ships );

		// Unset the flag.
		this.areFiltersSyncing = false;
	}

	/**
	 * Handles the change event for the currency dropdown.
	 */
	currencyDropdownUpdate() {
		// Check if the currency dropdown is null.
		if ( ! this.currencyDropdown || this.areFiltersSyncing ) {
			// bail.
			return;
		}

		// Get the updated value.
		const currencyValue = this.currencyDropdown.value[0] ?? 'USD';

		// Update the state.
		updateCurrency( currencyValue );
	}

	/**
	 * Handles the change event for the currency radio inputs.
	 *
	 * @param { Event } event The event object.
	 */
	currencyRadioUpdate( event: Event ) {
		// Null check.
		if ( ! event.target || this.areFiltersSyncing ) {
			// bail.
			return;
		}

		// Get the radio input.
		const theRadioInput = event.target as HTMLInputElement;

		// Is the radio checked.
		if ( ! theRadioInput.checked ) {
			// No, bail.
			return;
		}

		// Get the currency value.
		const currencyValue = theRadioInput.value !== 'on' ? theRadioInput.value : 'USD';

		// Update the state.
		updateCurrency( currencyValue );
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
	 * @returns {string[]} selectedFilters
	 */
	getSelectedFilterValues( filterList: NodeListOf<HTMLInputElement> ) {
		// Initialize the selectedFilters.
		const selectedFilters: string[] = [];

		// Loop through the inputs.
		filterList.forEach( checkboxInput => {
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
	 * @param {NodeListOf<HTMLInputElement>} filterList     The filter list.
	 * @param {string[]}                     selectedValues The selected values.
	 */
	updateCheckboxes( filterList: NodeListOf<HTMLInputElement>, selectedValues: string[] = [] ) {
		// Clear the checkboxes.
		filterList.forEach( checkboxInput => {
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
