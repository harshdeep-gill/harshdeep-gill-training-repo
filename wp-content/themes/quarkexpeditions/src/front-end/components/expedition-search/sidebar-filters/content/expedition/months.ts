/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { addMonth, removeMonth } from '../../../actions';

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
	private readonly inputs: NodeListOf<HTMLInputElement>;
	private isFilterUpdating: boolean;
	private readonly filterCountElement: HTMLElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Initialize properties.
		this.inputs = this.querySelectorAll( 'input[type="checkbox"][name="months"]' );
		this.isFilterUpdating = false;
		this.filterCountElement = this.querySelector( '.expedition-search__filter-count' );

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
		const { months } = state;

		// Set updating flag.
		this.isFilterUpdating = true;

		// Update the filters.
		this.inputs.forEach( ( input ) => {
			// Check if the input is in the state.
			if ( months.find( ( month ) => month.value === input.value ) ) {
				input.checked = true;
			} else {
				input.checked = false;
			}
		} );

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
			// Initialize month object.
			const month: ExpeditionSearchFilterState = {
				label: input.getAttribute( 'data-label' ) ?? '',
				value: input.value,
			};

			// Add the month.
			addMonth( month );
		} else {
			// Remove the month.
			removeMonth( input.value );
		}
	}
}
