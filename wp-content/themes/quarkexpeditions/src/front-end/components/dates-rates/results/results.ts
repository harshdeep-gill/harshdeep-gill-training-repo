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
import { initialize, markupUpdated } from '../actions';

/**
 * Results Class
 */
export default class DatesRatesResultsElement extends HTMLElement {
	/**
	 * Properties
	 */
	private readonly partial: string;
	private readonly selector: string;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super.
		super();

		// Initialize properties.
		this.partial = this.getAttribute( 'partial' ) ?? '';
		this.selector = this.getAttribute( 'selector' ) ?? '';

		// Get server rendered values if available.
		let isServerRendered = this.getAttribute( 'server-rendered' ) === 'yes';
		const serverRenderData = {
			page: Number.NaN,
			totalPages: Number.NaN,
			resultCount: Number.NaN,
			perPage: Number.NaN,
		};

		// Is it server rendered?
		if ( isServerRendered && serverRenderData ) {
			serverRenderData.page = parseInt( this.getAttribute( 'page' ) ?? '1' );
			serverRenderData.totalPages = parseInt( this.getAttribute( 'total-pages' ) ?? '1' );
			serverRenderData.resultCount = parseInt( this.getAttribute( 'result-count' ) ?? '0' );
			serverRenderData.perPage = parseInt( this.getAttribute( 'per-page' ) ?? '1' );

			// Check if we have valid numbers.
			isServerRendered = ! (
				Number.isNaN( serverRenderData.page ) ||
				Number.isNaN( serverRenderData.totalPages ) ||
				Number.isNaN( serverRenderData.resultCount ) ||
				Number.isNaN( serverRenderData.perPage )
			);
		}

		// Subscribe to the store.
		subscribe( this.update.bind( this ) );

		// Initialize the settings.
		initialize( {
			partial: this.partial,
			selector: this.selector,
			serverRenderData: isServerRendered ? serverRenderData : undefined,
		} );
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
