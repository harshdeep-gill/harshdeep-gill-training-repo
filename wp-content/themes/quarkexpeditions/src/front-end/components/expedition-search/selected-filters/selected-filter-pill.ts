/**
 * Global variables.
 */
const { HTMLElement } = window;

/**
 * Internal dependencies
 */
import { removeAdventureOption, removeCabinClass, removeDestination, removeExpedition, removeItineraryLength, removeLanguage, removeMonth, removeShip, removeTraveler } from '../actions';

/**
 * ExpeditionSearchSelectedFilterPill Class.
 */
export default class ExpeditionSearchSelectedFilterPill extends HTMLElement {
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
		this.closeButton = this.querySelector( '.expedition-search__selected-filter-close' );

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
			// Destinations.
			case 'destinations':
				removeDestination( pillValue );
				break;

			// Months.
			case 'months':
				removeMonth( pillValue );
				break;

			// Itinerary lengths.
			case 'itineraryLengths':
				removeItineraryLength( pillValue );
				break;

			// Ships.
			case 'ships':
				removeShip( pillValue );
				break;

			// Adventure Options.
			case 'adventureOptions':
				removeAdventureOption( pillValue );
				break;

			// Languages.
			case 'languages':
				removeLanguage( pillValue );
				break;

			// Expeditions.
			case 'expeditions':
				removeExpedition( pillValue );
				break;

			// Cabin classes.
			case 'cabinClasses':
				removeCabinClass( pillValue );
				break;

			// Travelers.
			case 'travelers':
				removeTraveler( pillValue );
				break;

			// Default, do nothing.
			default:
				break;
		}
	}
}
