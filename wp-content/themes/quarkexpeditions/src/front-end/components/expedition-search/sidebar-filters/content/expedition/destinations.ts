/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { addDestination, addDestinations, removeDestination } from '../../../actions';
import ExpeditionSearchSidebarFiltersInputsContainerElement from '../../inputs-container';

/**
 * Get Store.
 */
const { subscribe, getState } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearchFilterDestinations Class.
 */
export default class ExpeditionSearchFilterDestinations extends HTMLElement {
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
		this.inputs = this.querySelectorAll( 'input[type="checkbox"][name="destinations"]' );
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
		const { destinations } = state;

		// Set updating flag.
		this.isFilterUpdating = true;

		// Update the filters.
		this.inputs.forEach( ( input ) => {
			// Get the parent value.
			const parentValue = input.getAttribute( 'data-parent' ) ?? '';

			// Check if the input or its parent is in the state.
			if ( destinations.find( ( destination ) => destination.value === input.value || destination.value === parentValue ) ) {
				input.checked = true;
			} else {
				input.checked = false;
			}
		} );

		// Null check.
		if ( this.filterCountElement ) {
			// check and update count
			if ( destinations.length > 0 ) {
				this.filterCountElement.innerHTML = `(${ destinations.length })`;
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

		// Get the parent destination.
		const parentValue = input.getAttribute( 'data-parent' ) ?? '';
		let parentElement = null;

		// Does this have a parent value?
		if ( parentValue ) {
			parentElement = this.querySelector<HTMLInputElement>( `input[type="checkbox"][name="destinations"][value="${ parentValue }"]` );
		}

		// Is this input checked?
		if ( input.checked ) {
			// Initialize destination object.
			const destination: ExpeditionSearchFilterState = {
				label: input.getAttribute( 'data-label' ) ?? '',
				value: input.value ?? '',
			};

			// Check if it has a parent and set the value accordingly.
			if ( parentElement ) {
				destination.parent = parentValue ?? undefined;

				// Sibling count.
				let siblingCount = 0;

				// Loop and calculate.
				this.inputs.forEach( ( maybeSibling ) => {
					// Is this a sibling?
					if ( maybeSibling.getAttribute( 'data-parent' ) !== parentValue ) {
						// Nope.
						return;
					}

					// Yes. We are also including the current input.
					siblingCount++;
				} );

				// Get the state.
				const { destinations }: ExpeditionSearchState = getState();
				let selectedSiblingCount = 0;

				// Loop through selected destinations.
				destinations.forEach( ( maybeSelectedSibling ) => {
					// Is this a selected sibling?
					if ( maybeSelectedSibling.parent === parentValue && maybeSelectedSibling.value !== input.value ) {
						selectedSiblingCount++;
					}
				} );

				// If this sibling gets selected, then all will be selected.
				if ( selectedSiblingCount === siblingCount - 1 ) {
					// Selecte the parent instead.
					destination.value = parentElement.value;
					destination.label = parentElement.getAttribute( 'data-label' ) ?? '';
					delete destination.parent;
				}
			}

			// Add the destination.
			addDestination( destination );
		} else {
			// Get the state.
			const { destinations }: ExpeditionSearchState = getState();

			// Is this the parent element or was the parent element not selected?
			if ( ! parentElement || destinations.findIndex( ( existingDestination ) => existingDestination.value === parentValue ) === -1 ) {
				// Yes, remove it normally.
				removeDestination( input.value );

				// Bail.
				return;
			}

			/**
			 * If we are here, the parent was selected resulting in a select-all for the children which in turn removed their individual values
			 * from the state and added the parent's value in the state instead. Now, if we remove one of the children,
			 * we need to keep all other children in the state to prevent confusing behavior.
			 */
			const sibilngsToAdd: ExpeditionSearchFilterState[] = [];

			// Remove the parent.
			removeDestination( parentValue );

			// Loop through the inputs.
			this.inputs.forEach( ( maybeSibling ) => {
				// Is this a sibling input but not this one?
				if ( maybeSibling.value === input.value || maybeSibling.getAttribute( 'data-parent' ) !== parentValue ) {
					// Nope, we don't deal with this.
					return;
				}

				// Add the sibling.
				sibilngsToAdd.push( {
					label: maybeSibling.getAttribute( 'data-label' ) ?? '',
					value: maybeSibling.value,
					parent: parentValue,
				} );
			} );

			// Add the destinations.
			addDestinations( sibilngsToAdd );
		}

		// Set it as the last opened accordion item.
		this.closest<ExpeditionSearchSidebarFiltersInputsContainerElement>( 'quark-expedition-search-sidebar-filters-inputs-container' )?.setLastOpenedAccordionItemId( this.closest( 'tp-accordion-item' )?.id ?? '' );
	}
}
