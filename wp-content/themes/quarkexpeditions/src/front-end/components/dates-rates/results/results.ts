/**
 * Globals
 */
const { HTMLElement } = window;

/**
 * Internal dependencies.
 */
import { initializeFetchPartialSettings } from '../actions';

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
	}
}
