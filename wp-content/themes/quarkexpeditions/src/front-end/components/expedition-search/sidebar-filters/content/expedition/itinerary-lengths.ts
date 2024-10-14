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

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Initialize properties.
		this.rangeSlider = this.querySelector( 'quark-range-slider' );
		this.isFilterUpdating = false;

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

		// Get the min value and max value.
		let minValue = parseInt( this.rangeSlider.getAttribute( 'min' ) ?? '' );
		let maxValue = parseInt( this.rangeSlider.getAttribute( 'max' ) ?? '' );
		minValue = Number.isNaN( minValue ) ? 0 : minValue;
		maxValue = Number.isNaN( maxValue ) ? 0 : maxValue;

		// Do we have invalid state?
		if ( itineraryLengths[ 0 ] < minValue || itineraryLengths[ 1 ] > maxValue ) {
			updateItineraryLength( [ minValue, maxValue ] );

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
		if ( this.isFilterUpdating || ! evt.target ) {
			// Bail to avoid stack overflow.
			return;
		}

		// Check if it has the values.
		if (
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
	}
}
