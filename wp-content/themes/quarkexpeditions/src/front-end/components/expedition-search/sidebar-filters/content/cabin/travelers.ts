/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { addTraveler, removeTraveler } from '../../../actions';
import ExpeditionSearchSidebarFiltersInputsContainerElement from '../../inputs-container';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearchFilterTravelers Class.
 */
export default class ExpeditionSearchFilterTravelers extends HTMLElement {
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
		this.inputs = this.querySelectorAll( 'input[type="checkbox"][name="travelers"]' );
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
		const { travelers } = state;

		// Set updating flag.
		this.isFilterUpdating = true;

		// Update the filters.
		this.inputs.forEach( ( input ) => {
			// Check if the input is in the state.
			if ( travelers.find( ( traveler ) => traveler.value === input.value ) ) {
				input.checked = true;
			} else {
				input.checked = false;
			}
		} );

		// Null check.
		if ( this.filterCountElement ) {
			// check and update count
			if ( travelers.length > 0 ) {
				this.filterCountElement.innerHTML = `(${ travelers.length })`;
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
			// Initialize traveler object.
			const traveler: ExpeditionSearchFilterState = {
				label: input.getAttribute( 'data-label' ) ?? '',
				value: input.value,
			};

			// Add the traveler.
			addTraveler( traveler );
		} else {
			// Remove the traveler.
			removeTraveler( input.value );
		}

		// Set it as the last opened accordion item.
		this.closest<ExpeditionSearchSidebarFiltersInputsContainerElement>( 'quark-expedition-search-sidebar-filters-inputs-container' )?.setLastOpenedAccordionItemId( this.closest( 'tp-accordion-item' )?.id ?? '' );
	}
}
