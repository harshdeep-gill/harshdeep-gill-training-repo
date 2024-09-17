/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { addShip, getShipsState, removeShip } from '../actions';
import DatesRatesFiltersInputsContainerElement from './inputs-container';

/**
 * Store
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Ships filter class.
 */
export default class DatesRatesFilterShipsElement extends HTMLElement {
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
	 */
	update() {
		// Get the state.
		const ships = getShipsState();

		// Do we have no filters?
		if ( ships.length === 0 ) {
			// Yes, bail.
			return;
		}

		// Check if we should update.
		this.isFilterUpdating = true;

		// Loop through the checkboxes.
		this.filterCheckboxes.forEach( ( checkbox ) => {
			// Check if we should `check` the checkbox.
			if ( ships.find( ( ship ) => ship.value === checkbox.value ) ) {
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
			// Yes, add the ship.
			addShip( { value: checkbox.value, label: checkbox.dataset.label ?? '' } );
		} else {
			removeShip( checkbox.value );
		}

		// Set this as the last opened accordion.
		this.closest<DatesRatesFiltersInputsContainerElement>( 'quark-dates-rates-filters-inputs-container' )?.setLastOpenedAccordionItemId( this.closest( 'tp-accordion-item' )?.id ?? '' );
	}
}
