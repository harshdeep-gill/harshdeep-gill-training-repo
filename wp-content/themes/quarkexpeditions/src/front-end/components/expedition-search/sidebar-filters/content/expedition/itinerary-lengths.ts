/**
 * Global variables.
 */
const { HTMLElement, zustand } = window;

/**
 * Internal dependencies
 */
import { updateItineraryLength } from '../../../actions';

/**
 * External Dependencies
 */
import QuarkRangeSlider from '../../../../form/range-slider';
import { debounce } from '../../../../../global/utility';

/**
 * Internal dependencies
 */
import ExpeditionSearchSidebarFiltersInputsContainerElement from '../../inputs-container';

/**
 * Get Store.
 */
const { subscribe } = zustand.stores.expeditionSearch;

/**
 * ExpeditionSearchFilterItineraryLengths Class.
 */
export default class ExpeditionSearchFilterItineraryLengths extends HTMLElement {
	/**
	 * Properties.
	 */
	private readonly rangeSlider: QuarkRangeSlider | null;
	private isFilterUpdating: boolean;
	private handleSliderEvent: Function;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Initialize properties.
		this.rangeSlider = this.querySelector( 'quark-range-slider' );
		this.isFilterUpdating = false;

		// Setup debounced eventhandler
		this.handleSliderEvent = debounce( ( evt: Event ) => {
			// Check if it has the values.
			if (
				evt &&
				evt.target === this.rangeSlider &&
				'detail' in evt &&
				evt.detail instanceof Object &&
				'selectedValues' in evt.detail &&
				Array.isArray( evt.detail.selectedValues ) &&
				2 === evt.detail.selectedValues.length
			) {
				const { selectedValues } = evt.detail;

				// Initialize updated values.
				let updatedValues: [ number, number ] = [ 0, 0 ];

				// Check if we have valid values.
				if (
					'number' === typeof selectedValues[ 0 ] &&
					'number' === typeof selectedValues[ 1 ] &&
					! Number.isNaN( selectedValues[ 0 ] ) &&
					! Number.isNaN( selectedValues[ 1 ] )
				) {
					updatedValues = [ selectedValues[ 0 ], selectedValues[ 1 ] ];
				}

				// Update the itinerary length.
				updateItineraryLength( updatedValues );
			}
		} );

		// Setup events.
		this.rangeSlider?.addEventListener( 'change', this.handleInputChange.bind( this ) );

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
		const { itineraryLengths } = state;

		// Sanity check.
		if (
			! this.rangeSlider ||
			itineraryLengths.length !== 2 ||
			Number.isNaN( itineraryLengths[ 0 ] ) ||
			Number.isNaN( itineraryLengths[ 1 ] ) ||
			0 > itineraryLengths[ 0 ] ||
			itineraryLengths[ 0 ] > itineraryLengths[ 1 ]
		) {
			// Bail.
			return;
		}

		// Set updating flag.
		this.isFilterUpdating = true;

		// Update the filters.
		this.rangeSlider.setValues( [ itineraryLengths[ 0 ], itineraryLengths[ 1 ] ] );

		// Unset the updating flag.
		this.isFilterUpdating = false;
	}

	/**
	 * Handles the input chage event.
	 *
	 * @param { Event } evt The event object.
	 */
	handleInputChange( evt: Event ) {
		// Check if the input is syncing with state.
		if ( this.isFilterUpdating || ! evt.target || evt.target !== this.rangeSlider ) {
			// Bail to avoid stack overflow.
			return;
		}

		// Handle the slider's change event.
		this.handleSliderEvent( evt );

		// Set it as the last opened accordion item.
		this.closest<ExpeditionSearchSidebarFiltersInputsContainerElement>( 'quark-expedition-search-sidebar-filters-inputs-container' )?.setLastOpenedAccordionItemId( this.closest( 'tp-accordion-item' )?.id ?? '' );
	}
}
