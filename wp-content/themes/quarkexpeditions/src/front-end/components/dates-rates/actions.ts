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
import { camelToSnakeCase } from '../../global/utility';

/**
 * Update currency filter value.
 *
 * @param {string} updatedCurrency Selected Currency.
 */
export const updateCurrency = ( updatedCurrency: string ) => {
	// Get State.
	if ( ! updatedCurrency ) {
		updatedCurrency = 'USD';
	}

	// Set state.
	setState( {
		currency: updatedCurrency,
	} );

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

	// Set the state
	setState( {
		seasons: [ ...seasons, filter ],
		page: 1,
	} );

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

	// Set the state.
	setState( {
		seasons: seasons.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	} );

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

	// Set the state
	setState( {
		expeditions: [ ...expeditions, filter ],
		page: 1,
	} );

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

	// Set the state.
	setState( {
		expeditions: expeditions.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	} );

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

	// Set the state
	setState( {
		adventureOptions: [ ...adventureOptions, filter ],
		page: 1,
	} );

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

	// Set the state.
	setState( {
		adventureOptions: adventureOptions.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	} );

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

	// Set the state
	setState( {
		months: [ ...months, filter ],
		page: 1,
	} );

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

	// Set the state.
	setState( {
		months: months.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	} );

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

	// Set the state
	setState( {
		durations: [ ...durations, filter ],
		page: 1,
	} );

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

	// Set the state.
	setState( {
		durations: durations.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	} );

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

	// Set the state
	setState( {
		ships: [ ...ships, filter ],
		page: 1,
	} );

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

	// Set the state.
	setState( {
		ships: ships.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
		page: 1,
	} );

	// Update URL.
	updateUrlByFilters();

	// Fetch results.
	fetchResults();
};

/**
 * Clears all the filters.
 */
export const clearAllFilters = () => {
	// Set the state.
	setState( {
		seasons: [],
		expeditions: [],
		adventureOptions: [],
		months: [],
		durations: [],
		ships: [],
		perPage: 10,
		page: 1,
	} );

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

	// Set the state
	setState( {
		page: updatedPage,
	} );

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

	// Set the state
	setState( {
		page: page - 1,
	} );

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

	// Set the state
	setState( {
		page: page + 1,
	} );

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

	// Set the state
	setState( {
		perPage: updatedValue,
		page: 1,
	} );

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
	const updateObject: { [key: string]: any } = {
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
	setState( {
		isLoading: true,
	} );

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
	).catch( () => setState( { isLoading: false, shouldMarkupUpdate: false } ) );
};

/**
 * Returns an array for values for a list of filters passed in.
 *
 * @param {Object[]} list
 *
 * @return {string[]|number[]} The array of values.
 */
const pluckValues = ( list: DatesRatesFilterState[] ): string[] | number[] => list.map( ( filter ) => filter.value );

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

	// Set state.
	setState( {
		markup: resultCount !== 0 ? markup : '',
		noResultsMarkup: noResultsMarkup ?? '',
		resultCount,
		totalPages: resultCount !== 0 ? Math.ceil( resultCount / perPage ) : 1,
		page: resultCount !== 0 ? page : 1,
		shouldMarkupUpdate: true,
	} );
};

/**
 * Action to indicated markup update completion.
 *
 */
export const markupUpdated = () => {
	// Set state
	setState( {
		isLoading: false,
		shouldMarkupUpdate: false,
	} );
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
	}: DatesRatesState = getState();

	// Build url from filters.
	const urlWithParams: string = buildUrlFromFilters( {
		seasons: pluckValues( seasons ),
		expeditions: pluckValues( expeditions ),
		adventureOptions: pluckValues( adventureOptions ),
		months: pluckValues( months ),
		durations: pluckValues( durations ),
		ships: pluckValues( ships ),
	} );

	// Update the url with selected/added filters.
	if ( urlWithParams ) {
		updateUrl( urlWithParams );
	}
};

/**
 * Builds the URL from the selected filters.
 *
 * @param {Object}              filters                  The selected filters.
 *
 * @param {string[] | number[]} filters.seasons          seasons.
 * @param {string[] | number[]} filters.expeditions      expeditions.
 * @param {string[] | number[]} filters.adventureOptions adventure options.
 * @param {string[] | number[]} filters.months           departure months.
 * @param {string[] | number[]} filters.durations        departure durations.
 * @param {string[] | number[]} filters.ships            ships.
 *
 * @return {string} The URL with params.
 */
const buildUrlFromFilters = (
	filters: {
		seasons: string[] | number[],
		expeditions: string[] | number[],
		adventureOptions: string[] | number[],
		months: string[] | number[],
		durations: string[] | number[],
		ships: string[] | number[],
	}
): string => {
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
		const stringifiedFilters = filters[ key ].toString();

		// Set the url params value based on key.
		if ( stringifiedFilters ) {
			urlParams[ snakeCasedKey ] = stringifiedFilters;
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
