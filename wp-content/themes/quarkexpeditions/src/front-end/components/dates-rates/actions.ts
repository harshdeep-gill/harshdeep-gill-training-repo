/**
 * Globals.
 */
const {
	zustand,
	fetchPartial,
	queryString,
} = window;

/**
 * Internal dependencies.
 */
const { setState, getState } = zustand.stores.datesRates;

/**
 * External dependencies
 */
import { camelToSnakeCase, convertPropertiesFromSnakeCaseToCamelCase } from '../../global/utility';

/**
 * Update currency filter value.
 *
 * TODO: Remove this when the global currency switcher is available.
 *
 * @param {string} updatedCurrency Selected Currency.
 */
export const updateCurrency = ( updatedCurrency: string ) => {
	// Get State.
	if ( ! updatedCurrency ) {
		updatedCurrency = 'USD';
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		currency: updatedCurrency,
	};

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Adds a season to the seasons list.
 *
 * @param {Object} filter A singular filter.
 */
export const addSeason = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { seasons }: DatesRatesState = getState();

	// Check if the given filter is already selected.
	if ( seasons.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
		// It is, bail.
		return;
	}

	// Create the updateObject.
	const updateObject: DatesRatesStateUpdateObject = {
		seasons: [ ...seasons, filter ],
		page: 1,
	};

	// Set the state
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Remove a particular season given its value.
 *
 * @param {string} filterValue
 */
export const removeSeason = ( filterValue: string ) => {
	// Get the state.
	const { seasons }: DatesRatesState = getState();

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		seasons: seasons.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Adds a expedition to the expeditions list.
 *
 * @param {Object} filter A singular filter.
 */
export const addExpedition = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { expeditions }: DatesRatesState = getState();

	// Check if the given filter is already selected.
	if ( expeditions.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
		// It is, bail.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		expeditions: [ ...expeditions, filter ],
		page: 1,
	};

	// Set the state
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Remove a particular expedition given its value.
 *
 * @param {string} filterValue
 */
export const removeExpedition = ( filterValue: string ) => {
	// Get the state.
	const { expeditions }: DatesRatesState = getState();

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		expeditions: expeditions.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Adds a adventureOption to the adventureOptions list.
 *
 * @param {Object} filter A singular filter.
 */
export const addAdventureOption = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { adventureOptions }: DatesRatesState = getState();

	// Check if the given filter is already selected.
	if ( adventureOptions.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
		// It is, bail.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		adventureOptions: [ ...adventureOptions, filter ],
		page: 1,
	};

	// Set the state
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Remove a particular adventureOption given its value.
 *
 * @param {string} filterValue
 */
export const removeAdventureOption = ( filterValue: string ) => {
	// Get the state.
	const { adventureOptions }: DatesRatesState = getState();

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		adventureOptions: adventureOptions.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Adds a month to the months list.
 *
 * @param {Object} filter A singular filter.
 */
export const addDepartureMonth = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { months }: DatesRatesState = getState();

	// Check if the given filter is already selected.
	if ( months.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
		// It is, bail.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		months: [ ...months, filter ],
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Remove a particular month given its value.
 *
 * @param {string} filterValue
 */
export const removeDepartureMonth = ( filterValue: string ) => {
	// Get the state.
	const { months }: DatesRatesState = getState();

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		months: months.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Adds a duration to the durations list.
 *
 * @param {Object} filter A singular filter.
 */
export const addDuration = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { durations }: DatesRatesState = getState();

	// Check if the given filter is already selected.
	if ( durations.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
		// It is, bail.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		durations: [ ...durations, filter ],
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Remove a particular duration given its value.
 *
 * @param {string} filterValue
 */
export const removeDuration = ( filterValue: string ) => {
	// Get the state.
	const { durations }: DatesRatesState = getState();

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		durations: durations.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Adds a ship to the ships list.
 *
 * @param {Object} filter A singular filter.
 */
export const addShip = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { ships }: DatesRatesState = getState();

	// Check if the given filter is already selected.
	if ( ships.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
		// It is, bail.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		ships: [ ...ships, filter ],
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Remove a particular ship given its value.
 *
 * @param {string} filterValue
 */
export const removeShip = ( filterValue: string ) => {
	// Get the state.
	const { ships }: DatesRatesState = getState();

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		ships: ships.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Clears all the filters.
 */
export const clearAllFilters = () => {
	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		seasons: [],
		expeditions: [],
		adventureOptions: [],
		months: [],
		durations: [],
		ships: [],
		perPage: 4,
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Sets the page.
 *
 * @param {number} updatedPage The updated page number.
 */
export const setPage = ( updatedPage: number ) => {
	// Get the state.
	const { totalPages }: DatesRatesState = getState();

	// Is this a valid updated page number?
	if ( Number.isNaN( updatedPage ) || updatedPage > totalPages || updatedPage < 1 ) {
		// No, reject the update.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		page: updatedPage,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Set the page to the previous page number.
 */
export const setPreviousPage = () => {
	// Get the state.
	const { page }: DatesRatesState = getState();

	// Is this the first page?
	if ( page === 1 ) {
		// There is no previous page.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		page: page - 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Set the page to the next page number.
 */
export const setNextPage = () => {
	// Get the state.
	const { page, totalPages }: DatesRatesState = getState();

	// Is this the last page?
	if ( page === totalPages ) {
		// There is no next page.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		page: page + 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Set the items per page state.
 *
 * @param {number} updatedValue The updated value.
 */
export const setPerPage = ( updatedValue: number ) => {
	// Is this a valid updated totalPages?
	if ( Number.isNaN( updatedValue ) || updatedValue < 1 ) {
		// No, reject the update.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		perPage: updatedValue,
		page: 1,
	};

	// Set the state.
	setState( updateObject );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Initializes the settings to be used for fetching the results.
 *
 * @param {Object} settings                              The settings.
 * @param {string} settings.partial                      The name of the partial.
 * @param {string} settings.selector                     The DOM selector of the container of the results.
 * @param {Object} settings.serverRenderData             The data passed during server render if done.
 * @param {number} settings.serverRenderData.page        The current page that was rendered on the server.
 * @param {number} settings.serverRenderData.totalPages  The total number of pages.
 * @param {number} settings.serverRenderData.resultCount The total number of results.
 * @param {number} settings.serverRenderData.perPage     The number of results per page.
 */
export const initialize = (
	settings: {
		partial: string,
		selector: string,
		serverRenderData?: {
			page: number,
			totalPages: number,
			resultCount: number,
			perPage: number,
		}
	}
) => {
	// Get the state.
	const { isInitialized }: DatesRatesState = getState();

	// Is it already initialized?
	if ( isInitialized ) {
		// Yes, no need to proceed further.
		return;
	}

	// Validation checks.
	if ( ! (
		settings.partial &&
		settings.selector
	) ) {
		// Invalid data.
		return;
	}

	// Initialize updateObject.
	const updateObject: DatesRatesStateUpdateObject = {
		partial: settings.partial,
		selector: settings.selector,
		isInitialized: true,
		baseUrl: window.location.origin + window.location.pathname,
	};

	// Check if we have server render data.
	if ( settings.serverRenderData ) {
		updateObject.page = settings.serverRenderData.page;
		updateObject.totalPages = settings.serverRenderData.totalPages;
		updateObject.resultCount = settings.serverRenderData.resultCount;
		updateObject.perPage = settings.serverRenderData.perPage;
	}

	// Get the saved state.
	const savedFilters = parseUrl();

	// Null check.
	if ( savedFilters ) {
		// Input containers for filters.
		const seasonsInputContainer = document.getElementById( 'filters-accordion-seasons' );
		const expeditionsInputContainer = document.getElementById( 'filters-accordion-expeditions' );
		const adventureOptionsInputContainer = document.getElementById( 'filters-accordion-adventure-options' );
		const monthsInputContainer = document.getElementById( 'filters-accordion-months' );
		const durationsInputContainer = document.getElementById( 'filters-accordion-durations' );
		const shipsInputContainer = document.getElementById( 'filters-accordion-ships' );

		/**
		 * Our filters are stored as individual lists of @type {DatesRatesFilterState} objects.
		 * We will update the `value` and `label` field based on the values from the `input` elements related to the filter.
		 *
		 * We need to do this because we need the label for each filter value to show in the selected filters. However,
		 * saving the labels in the URL is not a good idea.
		 */
		if ( seasonsInputContainer ) {
			// Map and filter the valid filters.
			updateObject.seasons = savedFilters.seasons.map( ( season ): DatesRatesFilterState => {
				// Get the input element.
				const filterInput = seasonsInputContainer.querySelector( `input[value="${ season }"]` );

				// Return the filter.
				return { value: season, label: filterInput?.getAttribute( 'data-label' ) ?? '' };
			} ).filter( ( season ) => season.label !== '' );
		}

		// Set up expeditions
		if ( expeditionsInputContainer ) {
			// Map and filter the valid filters.
			updateObject.expeditions = savedFilters.expeditions.map( ( expedition ): DatesRatesFilterState => {
				// Get the input element.
				const filterInput = expeditionsInputContainer.querySelector( `input[value="${ expedition }"]` );

				// Return the filter.
				return { value: expedition, label: filterInput?.getAttribute( 'data-label' ) ?? '' };
			} ).filter( ( expedition ) => expedition.label !== '' );
		}

		// Set up adventure Options
		if ( adventureOptionsInputContainer ) {
			// Map and filter valid filters.
			updateObject.adventureOptions = savedFilters.adventureOptions.map( ( adventureOption ): DatesRatesFilterState => {
				// Get the input element.
				const filterInput = adventureOptionsInputContainer.querySelector( `input[value="${ adventureOption }"]` );

				// Return the filter.
				return { value: adventureOption, label: filterInput?.getAttribute( 'data-label' ) ?? '' };
			} ).filter( ( adventureOption ) => adventureOption.label !== '' );
		}

		// Set up months
		if ( monthsInputContainer ) {
			// Map and filter valid filters.
			updateObject.months = savedFilters.months.map( ( month ): DatesRatesFilterState => {
				// Get the input element.
				const filterInput = monthsInputContainer.querySelector( `input[value="${ month }"]` );

				// Return the filter.
				return { value: month, label: filterInput?.getAttribute( 'data-label' ) ?? '' };
			} ).filter( ( month ) => month.label !== '' );
		}

		// Set up durations
		if ( durationsInputContainer ) {
			// Map and filter valid filters.
			updateObject.durations = savedFilters.durations.map( ( duration ): DatesRatesFilterState => {
				// Get the input element.
				const filterInput = durationsInputContainer.querySelector( `input[value="${ duration }"]` );

				// Return the filter.
				return { value: duration, label: filterInput?.getAttribute( 'data-label' ) ?? '' };
			} ).filter( ( duration ) => duration.label !== '' );
		}

		// Set up ships
		if ( shipsInputContainer ) {
			// Map and filter valid filters.
			updateObject.ships = savedFilters.ships.map( ( ship ): DatesRatesFilterState => {
				// Get the input element.
				const filterInput = shipsInputContainer.querySelector( `input[value="${ ship }"]` );

				// Return the filter.
				return { value: ship, label: filterInput?.getAttribute( 'data-label' ) ?? '' };
			} ).filter( ( ship ) => ship.label !== '' );
		}

		// Other filters.
		if ( ! settings.serverRenderData ) {
			// Set the page related filters.
			updateObject.perPage = savedFilters.perPage;
		}
	}

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	if ( ! settings.serverRenderData ) {
		fetchResults();
	}
};

/**
 * Fetches the results based on selected filters.
 */
const fetchResults = () => {
	// Get the state
	const {
		page,
		partial,
		selector,
		seasons,
		expeditions,
		adventureOptions,
		months,
		durations,
		ships,
		perPage,
		isInitialized,
		isLoading,
		currency,
	}: DatesRatesState = getState();

	// Sanity check.
	if ( ! isInitialized || isLoading ) {
		// Bail.
		return;
	}

	// Set loading.
	const updateObject: DatesRatesStateUpdateObject = {
		isLoading: true,
	};

	// Set the state.
	setState( updateObject );

	// Fetch the partial.
	fetchPartial(
		partial,
		{
			selectedFilters: {
				seasons: pluckValues( seasons ),
				expeditions: pluckValues( expeditions ),
				adventure_options: pluckValues( adventureOptions ),
				months: pluckValues( months ),
				durations: pluckValues( durations ),
				ships: pluckValues( ships ),
				posts_per_load: perPage,
				page,
				currency,
			},
		},
		resultsFetchedCallback,
		selector
	).catch( () => {
		// Update object.
		const errorUpdateObject: DatesRatesStateUpdateObject = { isLoading: false, shouldMarkupUpdate: false };

		// Set the state.
		setState( errorUpdateObject );
	} );
};

/**
 * Returns an array for values for a list of filters passed in.
 *
 * @param {Object[]} list
 *
 * @return {string[]|number[]} The array of values.
 */
const pluckValues = ( list: DatesRatesFilterState[] ): string[] => list.map( ( filter ) => filter.value );

/**
 * Callback to run after partial has been fetched.
 *
 * @param {Object} response The partial response.
 */
const resultsFetchedCallback = ( response: PartialData ) => {
	// Get state.
	const { perPage, page }: DatesRatesState = getState();

	// Get the data.
	const {
		markup,
		noResultsMarkup,
		data: { resultCount },
	} = response;

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		markup: resultCount !== 0 ? markup : '',
		noResultsMarkup: noResultsMarkup ?? '',
		resultCount,
		totalPages: resultCount !== 0 ? Math.ceil( resultCount / perPage ) : 1,
		page: resultCount !== 0 ? page : 1,
		shouldMarkupUpdate: true,
	};

	// Set the state.
	setState( updateObject );
};

/**
 * Action to indicated markup update completion.
 *
 */
export const markupUpdated = () => {
	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		isLoading: false,
		shouldMarkupUpdate: false,
	};

	// Set the state.
	setState( updateObject );
};

/**
 * Build url from selected filters and update url.
 */
const updateUrlByFilters = () => {
	// Get selected filters from state.
	const {
		seasons,
		expeditions,
		adventureOptions,
		months,
		durations,
		ships,
		perPage,
	}: DatesRatesState = getState();

	// Build url from filters.
	const urlWithParams: string = buildUrlFromFilters( {
		seasons: pluckValues( seasons ),
		expeditions: pluckValues( expeditions ),
		adventureOptions: pluckValues( adventureOptions ),
		months: pluckValues( months ),
		durations: pluckValues( durations ),
		ships: pluckValues( ships ),
		perPage,
	} );

	// Update the url with selected/added filters.
	if ( urlWithParams ) {
		updateUrl( urlWithParams );
	}
};

/**
 * Builds the URL from the selected filters.
 *
 * @param {Object}   filters                  The selected filters.
 *
 * @param {string[]} filters.seasons          seasons.
 * @param {string[]} filters.expeditions      expeditions.
 * @param {string[]} filters.adventureOptions adventure options.
 * @param {string[]} filters.months           departure months.
 * @param {string[]} filters.durations        departure durations.
 * @param {string[]} filters.ships            ships.
 * @param {number}   filters.perPage          items per page.
 *
 * @return {string} The URL with params.
 */
const buildUrlFromFilters = ( filters: DatesRatesFiltersSaved ): string => {
	// If queryString not available, early return.
	if ( ! queryString ) {
		// Return early.
		return '';
	}

	// Get current state.
	const currentState: DatesRatesState = getState();
	const { baseUrl } = currentState;

	// Prepare URL params.
	const urlParams: {
		[ key: string ]: string;
	} = {
		// Preserve other params if any.
		...queryString.parse( window.location.search ),
	};

	// Loop through selected filters and build url params.
	for ( const key in filters ) {
		// Convert camelCased key to snake_caked key.
		const snakeCasedKey: string = camelToSnakeCase( key );

		// @ts-ignore Stringified filters.
		const stringifiedFilter = filters[ key ].toString();

		// Set the url params value based on key.
		if ( stringifiedFilter ) {
			urlParams[ snakeCasedKey ] = stringifiedFilter;
		} else {
			delete urlParams[ snakeCasedKey ];
		}
	}

	/**
	 * Return url with params.
	 *
	 * e.g. https://example.com/?expeditions=23,22&seasons=2023-04,2024-05
	 */
	return queryString.stringifyUrl(
		{
			url: baseUrl,
			query: urlParams,
		},
	);
};

/**
 * Update Url.
 *
 * @param {string} url Url.
 */
const updateUrl = ( url: string ) => {
	// If url is not present, return.
	if ( ! url ) {
		// Early Return.
		return;
	}

	// If 'pushState' exists, use that, else send the url to the url using window.location.
	if ( window.history.pushState ) {
		window.history.pushState( { path: url }, '', url );
	} else {
		window.location.href = url;
	}
};

/**
 * Parse hash query string for user state.
 *
 * @return {Object} Return selected filters state passed from query string.
 */
const parseUrl = (): DatesRatesFiltersSaved | null => {
	// If search url or query string not available, return.
	if ( ! window.location.search || ! queryString ) {
		// Early return.
		return null;
	}

	// Get parsed state.
	const parsedState: {
		[ key: string ]: any
	} = convertPropertiesFromSnakeCaseToCamelCase( queryString.parse( window.location.search ) );

	// Initialize the saved filters object.
	const savedFilters: DatesRatesFiltersSaved = {
		seasons: [],
		expeditions: [],
		adventureOptions: [],
		months: [],
		durations: [],
		ships: [],
		perPage: 4,
	};

	// Get allowed params.
	const { allowedParams }: DatesRatesState = getState();

	// Loop through parsed state to build the selected filters from url.
	Object.keys( parsedState ).forEach( ( key: string ) => {
		// Check if the key is amongst the allowed params.
		if ( ! allowedParams.includes( key ) ) {
			// Early return.
			return;
		}

		// Add filters.
		switch ( key ) {
			case 'seasons':
				savedFilters.seasons = parsedState.seasons.split( ',' );
				break;
			case 'expeditions':
				savedFilters.expeditions = parsedState.expeditions.split( ',' );
				break;
			case 'adventureOptions':
				savedFilters.adventureOptions = parsedState.adventureOptions.split( ',' );
				break;
			case 'months':
				savedFilters.months = parsedState.months.split( ',' );
				break;
			case 'durations':
				savedFilters.durations = parsedState.durations.split( ',' );
				break;
			case 'ships':
				savedFilters.ships = parsedState.ships.split( ',' );
				break;
			case 'perPage':
				savedFilters.perPage = parsedState.perPage;
				break;
		}
	} );

	// Return selected filters state.
	return savedFilters;
};
