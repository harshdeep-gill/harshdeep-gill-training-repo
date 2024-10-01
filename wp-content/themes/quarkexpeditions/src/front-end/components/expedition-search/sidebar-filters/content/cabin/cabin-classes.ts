/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { addCabinClass, removeCabinClass } from '../../../actions';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearchFilterCabinClasses Class.
 */
export default class ExpeditionSearchFilterCabinClasses extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly inputs: NodeListOf<HTMLInputElement>;
	private isFilterUpdating: boolean;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Initialize properties.
		this.inputs = this.querySelectorAll( 'input[type="checkbox"][name="cabin-classes"]' );
		this.isFilterUpdating = false;

		// Setup events.
		this.inputs.forEach( ( input ) => input.addEventListener( 'change', this.handleInputChange.bind( this ) ) );

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );
	}

	/**
	 * Update component.
	 *
	 * @param {Object} state State.
	 */
	update( state: ExpeditionSearchState ): void {
		// Get state.
		const { cabinClasses } = state;

		// Set updating flag.
		this.isFilterUpdating = true;

		// Update the filters.
		this.inputs.forEach( ( input ) => {
			// Check if the input is in the state.
			if ( cabinClasses.find( ( cabinClass ) => cabinClass.value === input.value ) ) {
				input.checked = true;
			} else {
				input.checked = false;
			}
		} );

		// Unset the updating flag.
		this.isFilterUpdating = false;
	}

	/**
	 * Handles the input chage event.
	 *
	 * @param { Event } evt The event object.
	 */
	handleInputChange( evt: Event ) {
		// Check if the inputs are syncing with state.
		if ( this.isFilterUpdating || ! evt.target ) {
			// Bail to avoid stack overflow.
			return;
		}

		// Get the input element.
		const input = evt.target as HTMLInputElement;

		// Is this input checked?
		if ( input.checked ) {
			// Initialize cabinClass object.
			const cabinClass: ExpeditionSearchFilterState = {
				label: input.getAttribute( 'data-label' ) ?? '',
				value: input.value,
			};

			// Add the cabinClass.
			addCabinClass( cabinClass );
		} else {
			// Remove the cabinClass.
			removeCabinClass( input.value );
		}
	}
}
