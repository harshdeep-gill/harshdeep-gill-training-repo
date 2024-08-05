/**
 * Global variables.
 */
const { customElements, HTMLElement, fetchPartial } = window;

// TPMultiSelectElement Element.
import { TPMultiSelectElement } from '@travelopia/web-components';

/**
 * BookDeparturesExpeditions Class.
 */
export class BookDeparturesExpeditions extends HTMLElement {
	/**
	 * Properties.
	 *
	 * @private
	 */
	private url: string | null;
	private currencyDropdown: TPMultiSelectElement | null;
	private sortDropdown: TPMultiSelectElement | null;
	private resultsContainer: HTMLElement | null;
	private nextPage: number;
	private filters: object | null;

	/**
	 * Constructor
	 */
	constructor() {
		// Initialize super
		super();

		// Settings.
		this.url = window.quark?.fetchPartial?.url ?? '';
		this.nextPage = 0;
		this.filters = {
			currency: 'USD',
			sort: 'date-now',
		};

		// Elements.
		this.currencyDropdown = this.querySelector( '.book-departures-expeditions__filters-currency > tp-multi-select' );
		this.sortDropdown = this.querySelector( '.book-departures-expeditions__filters-sort > tp-multi-select' );
		this.resultsContainer = this.querySelector( '.book-departures-expeditions__results' );

		// Event Listeners.
		this.currencyDropdown?.addEventListener( 'change', this.updateFilters.bind( this ) );
		this.sortDropdown?.addEventListener( 'change', this.updateFilters.bind( this ) );
	}

	/**
	 * Update Filter Values.
	 *
	 * @param { Event } event Event
	 */
	updateFilters( event: Event ): void {
		// Early return.
		if ( this.currencyDropdown !== event.target && this.sortDropdown !== event.target ) {
			// Bail.
			return;
		}

		// Get the current target element.
		const currentTarget = event.target as TPMultiSelectElement;

		// Bail, if value doesn't exist.
		if ( ! currentTarget?.value[ 0 ] ) {
			// Bail.
			return;
		}

		// Update currency value.
		if ( this.currencyDropdown === currentTarget ) {
			// Update currency filter value.
			this.filters = {
				...this.filters,
				currency: currentTarget.value[ 0 ],
			};

			// Trigger Fetch Results.
			this.fetchResults();
		}

		// Update sort value.
		if ( this.sortDropdown === currentTarget ) {
			// Update sort filter value.
			this.filters = {
				...this.filters,
				sort: currentTarget.value[ 0 ],
			};

			// Trigger Fetch Results.
			this.fetchResults();
		}
	}

	/**
	 * Fetch posts.
	 */
	fetchResults() {
		// Get the settings data from dataset.
		const settingsData = this.resultsContainer?.dataset;

		// Early return.
		if ( ! settingsData ) {
			// Bail, if no data.
			return;
		}

		// Set all settings data to fetch results.
		const partialName: string | undefined = settingsData?.partial;
		const payload: any = JSON.parse( settingsData?.payload ?? '' );
		const resultsContainerSelector: string | undefined = settingsData?.selector;
		this.nextPage = payload?.page ?? 0;

		// Set loading and fetch partial.
		this.setAttribute( 'loading', 'true' );

		// Fetch partial.
		fetchPartial(
			partialName,
			{
				selectedFilters: {
					...payload,
					filters: this.filters,
					page: this.nextPage,
				},
				base_url: this.url,
			},
			this.updateResults.bind( this ),
			resultsContainerSelector,
		).catch( () => this.setAttribute( 'loading', 'false' ) );
	}

	/**
	 * Update Departure Results.
	 *
	 * @param { Object } response Response.
	 */
	updateResults( response: PartialData ): void {
		// Set Loading to false.
		this.setAttribute( 'loading', 'false' );

		// If items container does not exist, return.
		if ( ! this.resultsContainer ) {
			// Return.
			return;
		}

		// If markup not available return.
		if ( ! response.markup ) {
			// return.
			return;
		}

		// Append the response markup.
		this.resultsContainer.innerHTML = response.markup;

		// Set the value of the nextPage.
		this.nextPage = Number( response.data.nextPage ) || 0;
	}
}

// Define element.
customElements.define( 'quark-book-departures-expeditions', BookDeparturesExpeditions );
