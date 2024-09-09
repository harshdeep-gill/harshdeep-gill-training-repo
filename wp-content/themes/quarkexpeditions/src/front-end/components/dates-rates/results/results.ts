/**
 * Globals
 */
const { HTMLElement, zustand } = window;

/**
 * The store.
 */
const { subscribe } = zustand.stores.datesRates;

/**
 * Internal dependencies.
 */
import { initializeFetchPartialSettings, markupUpdated } from '../actions';

/**
 * Results Class
 */
export default class DatesRatesResultsElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly partial: string;
	private readonly selector: string;
	private readonly expeditionId: number;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.partial = this.getAttribute( 'partial' ) ?? '';
		this.selector = this.getAttribute( 'selector' ) ?? '';
		this.expeditionId = parseInt( this.getAttribute( 'expedition-id' ) ?? '' );

		// Initialize the settings.
		initializeFetchPartialSettings( {
			partial: this.partial,
			selector: this.selector,
			expeditionId: this.expeditionId,
		} );

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
		const { shouldMarkupUpdate } = state;

		// Should the markup be updated?
		if ( ! shouldMarkupUpdate ) {
			// No, bail.
			return;
		}

		// Get the markup.
		const { markup, noResultsMarkup } = state;

		// Check if markup is available.
		if ( markup ) {
			this.innerHTML = markup;
		} else {
			this.innerHTML = noResultsMarkup;
		}

		// Done.
		markupUpdated();
	}
}
