/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { addDepartureMonth, removeDepartureMonth } from '../actions';

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * DepartureMonths filter class.
 */
export default class DatesRatesFilterDepartureMonthsElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly filterCheckboxes: NodeListOf<HTMLInputElement>;
	private isFilterUpdating: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize Properties
		this.filterCheckboxes = this.querySelectorAll( 'input[type="checkbox"]' );
		this.isFilterUpdating = false;

		// Events.
		this.filterCheckboxes.forEach( ( checkbox ) => checkbox.addEventListener( 'change', this.handleCheckboxChange.bind( this ) ) );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Updates the component.
	 *
	 * @param {Object} state The state object.
	 */
	update( state: DatesRatesState ) {
		// Get the state.
		const { months } = state;

		// Check if we should update.
		this.isFilterUpdating = true;

		// Loop through the checkboxes.
		this.filterCheckboxes.forEach( ( checkbox ) => {
			// Check if we should `check` the checkbox.
			if ( months.find( ( month ) => month.value === checkbox.value ) ) {
				// We should.
				checkbox.checked = true;
			} else {
				checkbox.checked = false;
			}
		} );

		// Unset the flag.
		this.isFilterUpdating = false;
	}

	/**
	 * Handles the change event for a checkbox.
	 *
	 * @param {Event} event
	 */
	handleCheckboxChange( event: Event ) {
		// Null check.
		if ( ! event.target || this.isFilterUpdating ) {
			// Bail.
			return;
		}

		// Get the checkbox.
		const checkbox = event.target as HTMLInputElement;

		// Is the checkbox checked?
		if ( checkbox.checked ) {
			// Yes, add the month.
			addDepartureMonth( { value: checkbox.value, label: checkbox.dataset.label ?? '' } );
		} else {
			removeDepartureMonth( checkbox.value );
		}
	}
}
