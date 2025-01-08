/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * External dependencies
 */
import { MonthsMultiSelect } from '../../../../months-multi-select/main';
import { MonthsMultiSelectOption } from '../../../../months-multi-select/months-multi-select-option';

/**
 * Internal dependencies
 */
import { updateMonths } from '../../../actions';
import ExpeditionSearchSidebarFiltersInputsContainerElement from '../../inputs-container';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearchFilterMonths Class.
 */
export default class ExpeditionSearchFilterMonths extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly monthsMultiSelect: MonthsMultiSelect | null;
	private isFilterUpdating: boolean;
	private readonly filterCountElement: HTMLElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Initialize properties.
		this.isFilterUpdating = false;
		this.filterCountElement = this.querySelector( '.expedition-search__filter-count' );
		this.monthsMultiSelect = this.querySelector( 'quark-months-multi-select' );

		// Setup events.
		this.monthsMultiSelect?.addEventListener( 'change', this.handleMonthsSelectorChange.bind( this ) );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: ExpeditionSearchState ): void {
		// Null check.
		if ( ! this.monthsMultiSelect ) {
			// Bail.
			return;
		}

		// Get state.
		const { months } = state;

		// Set updating flag.
		this.isFilterUpdating = true;

		// New value for month selector.
		const newValue: string[] = [];

		// Loop through the options to select.
		months.forEach( ( month ) => {
			// Empty check.
			if ( ! month.value || ! month.label ) {
				// Bail.
				return;
			}

			// Select the month option.
			newValue.push( month.value );
		} );

		// Set the value
		this.monthsMultiSelect.value = newValue;

		// Null check.
		if ( this.filterCountElement ) {
			// check and update count
			if ( months.length > 0 ) {
				this.filterCountElement.innerHTML = `(${ months.length })`;
			} else {
				this.filterCountElement.innerHTML = '';
			}
		}

		// Unset the updating flag.
		this.isFilterUpdating = false;
	}

	/**
	 * Handles the months selector's change event
	 *
	 * @param {Event} evt The event object
	 */
	handleMonthsSelectorChange( evt: Event ) {
		// Null check.
		if ( ! evt.target || ! this.monthsMultiSelect || this.isFilterUpdating ) {
			// Bail.
			return;
		}

		// Get the values
		const values = this.monthsMultiSelect.value;

		// Check if we have the values
		if ( 0 === values.length ) {
			// Update the state
			updateMonths( [] );

			// Bail.
			return;
		}

		// Get the attribute selector based on the values.
		const valueAttributeSelector = values.map( ( value ) => {
			// Empty check.
			if ( ! value ) {
				// Bail.
				return '';
			}

			// Return the value attribute selector.
			return `quark-months-multi-select-option[value="${ value }"]`;
		} ).filter( ( singleSelector ) => singleSelector !== '' ).join( ',' );

		// Empty check.
		if ( ! valueAttributeSelector ) {
			// Bail.
			return;
		}

		// Get the selected options
		const selectedOptions: NodeListOf<MonthsMultiSelectOption> = this.querySelectorAll( valueAttributeSelector );

		// Initialize months
		const months: ExpeditionSearchFilterState[] = [];

		// Loop through the selected options
		selectedOptions.forEach( ( selectedOption ) => {
			// Get the attributes of the option.
			const value = selectedOption.getAttribute( 'value' );
			const label = selectedOption.getAttribute( 'label' );

			// Empty checks
			if ( ! value || ! label ) {
				// Bail.
				return;
			}

			// Add the month
			months.push( { value, label } );
		} );

		// Update the months
		updateMonths( months );

		// Set it as the last opened accordion item.
		this.closest<ExpeditionSearchSidebarFiltersInputsContainerElement>( 'quark-expedition-search-sidebar-filters-inputs-container' )?.setLastOpenedAccordionItemId( this.closest( 'tp-accordion-item' )?.id ?? '' );
	}
}
