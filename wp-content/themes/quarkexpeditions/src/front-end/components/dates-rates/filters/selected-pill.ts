/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * Internal Dependencies
 */
import { removeAdventureOption, removeDepartureMonth, removeDuration, removeExpedition, removeSeason, removeShip } from '../actions';

/**
 * Selected Filter Pill Class.
 */
export default class DatesRatesSelectedFilterPillElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly closeButton: HTMLButtonElement | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize parent.
		super();

		// Initialize properties.
		this.closeButton = this.querySelector( '.dates-rates__selected-filter-close' );

		// Setup Events.
		this.closeButton?.addEventListener( 'click', this.handleClosePill.bind( this ) );
	}

	/**
	 * Handles the close pill action.
	 */
	handleClosePill() {
		// Get the attributes.
		const pillFilter = this.getAttribute( 'filter' ) ?? '';
		const pillValue = this.getAttribute( 'value' ) ?? '';

		// Switch the filter
		switch ( pillFilter ) {
			// It is a seasons filter.
			case 'seasons':
				removeSeason( pillValue );
				break;

			// It is a expditions filter.
			case 'expeditions':
				removeExpedition( pillValue );
				break;

			// It is a adventure_options filter.
			case 'adventure_options':
				removeAdventureOption( pillValue );
				break;

			// It is a months filter.
			case 'months':
				removeDepartureMonth( pillValue );
				break;

			// It is a durations filter.
			case 'durations':
				removeDuration( pillValue );
				break;

			// It is a ships filter.
			case 'ships':
				removeShip( pillValue );
				break;

			// Default, do nothing.
			default:
				break;
		}
	}
}
