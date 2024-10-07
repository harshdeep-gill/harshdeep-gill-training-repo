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
const { setState, getState, subscribe } = zustand.stores.datesRates;
import { DEFAULT_STATE } from './data';

/**
 * External dependencies
 */
import { camelToSnakeCase, convertPropertiesFromSnakeCaseToCamelCase } from '../../global/utility';

/**
 * Gets the selected seasons values.
 *
 * @return {Object[]} The filter values.
 */
export const getSeasons = (): DatesRatesFilterState[] => {
	// Get the seasons.
	const { seasons }: DatesRatesState = getState();

	// Return the filter values.
	return seasons;
};

/**
 * Adds a season to the seasons list.
 *
 * @param {Object} filter A singular filter.
 */
export const addSeason = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { seasons, selectedFilters }: DatesRatesState = getState();

	// Check for validity.
	if (
		! filter.label ||
		! filter.value ||
		seasons.findIndex( ( season ) => season.value === filter.value ) > -1
	) {
		// Bail.
		return;
	}

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		seasons: [ ...seasons, filter ],
		pageNumber: DEFAULT_STATE.pageNumber,
		selectedFilters: [ ...selectedFilters ],
	};

	// Check if it is already selected.
	if ( updateObject.selectedFilters && ! updateObject.selectedFilters.includes( 'seasons' ) ) {
		// Nope, push it.
		updateObject.selectedFilters.push( 'seasons' );
	}

	// Set the state.
	setState( updateObject );

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
	const { seasons, selectedFilters }: DatesRatesState = getState();

	// Get the new seasons.
	const newSeasons = seasons.filter( ( season ) => season.value !== filterValue );

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		seasons: newSeasons,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Remove the selected filter if no filters are selected.
	if ( newSeasons.length === 0 ) {
		updateObject.selectedFilters = selectedFilters.filter( ( selectedFilter ) => selectedFilter !== 'seasons' );
	}

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Gets the selected expeditions values.
 *
 * @return {Object[]} The filter values.
 */
export const getExpeditions = (): DatesRatesFilterState[] => {
	// Get the expeditions.
	const { expeditions }: DatesRatesState = getState();

	// Return the filter values.
	return expeditions;
};

/**
 * Adds a expedition to the expeditions list.
 *
 * @param {Object} filter A singular filter.
 */
export const addExpedition = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { expeditions, selectedFilters }: DatesRatesState = getState();

	// Check for validity.
	if (
		! filter.label ||
		! filter.value ||
		expeditions.findIndex( ( expedition ) => expedition.value === filter.value ) > -1
	) {
		// Bail.
		return;
	}

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		expeditions: [ ...expeditions, filter ],
		pageNumber: DEFAULT_STATE.pageNumber,
		selectedFilters: [ ...selectedFilters ],
	};

	// Check if it is already selected.
	if ( updateObject.selectedFilters && ! updateObject.selectedFilters.includes( 'expeditions' ) ) {
		// Nope, push it.
		updateObject.selectedFilters.push( 'expeditions' );
	}

	// Set the state.
	setState( updateObject );

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
	const { expeditions, selectedFilters }: DatesRatesState = getState();

	// Get the new expeditions.
	const newexpeditions = expeditions.filter( ( expedition ) => expedition.value !== filterValue );

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		expeditions: newexpeditions,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Remove the selected filter if no filters are selected.
	if ( newexpeditions.length === 0 ) {
		updateObject.selectedFilters = selectedFilters.filter( ( selectedFilter ) => selectedFilter !== 'expeditions' );
	}

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Gets the selected adventureOptions values.
 *
 * @return {Object[]} The filter values.
 */
export const getAdventureOptions = (): DatesRatesFilterState[] => {
	// Get the adventureOptions.
	const { adventureOptions }: DatesRatesState = getState();

	// Return the filter values.
	return adventureOptions;
};

/**
 * Adds a adventureOption to the adventureOptions list.
 *
 * @param {Object} filter A singular filter.
 */
export const addAdventureOption = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { adventureOptions, selectedFilters }: DatesRatesState = getState();

	// Check for validity.
	if (
		! filter.label ||
		! filter.value ||
		adventureOptions.findIndex( ( adventureOption ) => adventureOption.value === filter.value ) > -1
	) {
		// Bail.
		return;
	}

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		adventureOptions: [ ...adventureOptions, filter ],
		pageNumber: DEFAULT_STATE.pageNumber,
		selectedFilters: [ ...selectedFilters ],
	};

	// Check if it is already selected.
	if ( updateObject.selectedFilters && ! updateObject.selectedFilters.includes( 'adventureOptions' ) ) {
		// Nope, push it.
		updateObject.selectedFilters.push( 'adventureOptions' );
	}

	// Set the state.
	setState( updateObject );

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
	const { adventureOptions, selectedFilters }: DatesRatesState = getState();

	// Get the new adventureOptions.
	const newAdventureOptions = adventureOptions.filter( ( adventureOption ) => adventureOption.value !== filterValue );

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		adventureOptions: newAdventureOptions,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Remove the selected filter if no filters are selected.
	if ( newAdventureOptions.length === 0 ) {
		updateObject.selectedFilters = selectedFilters.filter( ( selectedFilter ) => selectedFilter !== 'adventureOptions' );
	}

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Gets the selected months values.
 *
 * @return {Object[]} The filter values.
 */
export const getDepartureMonths = (): DatesRatesFilterState[] => {
	// Get the months.
	const { months }: DatesRatesState = getState();

	// Return the filter values.
	return months;
};

/**
 * Adds a month to the months list.
 *
 * @param {Object} filter A singular filter.
 */
export const addDepartureMonth = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { months, selectedFilters }: DatesRatesState = getState();

	// Check for validity.
	if (
		! filter.label ||
		! filter.value ||
		months.findIndex( ( month ) => month.value === filter.value ) > -1
	) {
		// Bail.
		return;
	}

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		months: [ ...months, filter ],
		pageNumber: DEFAULT_STATE.pageNumber,
		selectedFilters: [ ...selectedFilters ],
	};

	// Check if it is already selected.
	if ( updateObject.selectedFilters && ! updateObject.selectedFilters.includes( 'months' ) ) {
		// Nope, push it.
		updateObject.selectedFilters.push( 'months' );
	}

	// Set the state.
	setState( updateObject );

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
	const { months, selectedFilters }: DatesRatesState = getState();

	// Get the new months.
	const newMonths = months.filter( ( month ) => month.value !== filterValue );

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		months: newMonths,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Remove the selected filter if no filters are selected.
	if ( newMonths.length === 0 ) {
		updateObject.selectedFilters = selectedFilters.filter( ( selectedFilter ) => selectedFilter !== 'months' );
	}

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Gets the selected durations values.
 *
 * @return {Object[]} The filter values.
 */
export const getDurations = (): DatesRatesFilterState[] => {
	// Get the durations.
	const { durations }: DatesRatesState = getState();

	// Return the filter values.
	return durations;
};

/**
 * Adds a duration to the durations list.
 *
 * @param {Object} filter A singular filter.
 */
export const addDuration = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { durations, selectedFilters }: DatesRatesState = getState();

	// Check for validity.
	if (
		! filter.label ||
		! filter.value ||
		durations.findIndex( ( duration ) => duration.value === filter.value ) > -1
	) {
		// Bail.
		return;
	}

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		durations: [ ...durations, filter ],
		pageNumber: DEFAULT_STATE.pageNumber,
		selectedFilters: [ ...selectedFilters ],
	};

	// Check if it is already selected.
	if ( updateObject.selectedFilters && ! updateObject.selectedFilters.includes( 'durations' ) ) {
		// Nope, push it.
		updateObject.selectedFilters.push( 'durations' );
	}

	// Set the state.
	setState( updateObject );

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
	const { durations, selectedFilters }: DatesRatesState = getState();

	// Get the new durations.
	const newDurations = durations.filter( ( duration ) => duration.value !== filterValue );

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		durations: newDurations,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Remove the selected filter if no filters are selected.
	if ( newDurations.length === 0 ) {
		updateObject.selectedFilters = selectedFilters.filter( ( selectedFilter ) => selectedFilter !== 'durations' );
	}

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Gets the selected ships values.
 *
 * @return {Object[]} The filter values.
 */
export const getShips = (): DatesRatesFilterState[] => {
	// Get the ships.
	const { ships }: DatesRatesState = getState();

	// Return the filter values.
	return ships;
};

/**
 * Adds a ship to the ships list.
 *
 * @param {Object} filter A singular filter.
 */
export const addShip = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { ships, selectedFilters }: DatesRatesState = getState();

	// Check for validity.
	if (
		! filter.label ||
		! filter.value ||
		ships.findIndex( ( ship ) => ship.value === filter.value ) > -1
	) {
		// Bail.
		return;
	}

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		ships: [ ...ships, filter ],
		pageNumber: DEFAULT_STATE.pageNumber,
		selectedFilters: [ ...selectedFilters ],
	};

	// Check if it is already selected.
	if ( updateObject.selectedFilters && ! updateObject.selectedFilters.includes( 'ships' ) ) {
		// Nope, push it.
		updateObject.selectedFilters.push( 'ships' );
	}

	// Set the state.
	setState( updateObject );

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
	const { ships, selectedFilters }: DatesRatesState = getState();

	// Get the new ships.
	const newShips = ships.filter( ( ship ) => ship.value !== filterValue );

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		ships: newShips,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Remove the selected filter if no filters are selected.
	if ( newShips.length === 0 ) {
		updateObject.selectedFilters = selectedFilters.filter( ( selectedFilter ) => selectedFilter !== 'ships' );
	}

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Clears all the filters.
 */
export const clearAllFilters = () => {
	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		selectedFilters: [ ...DEFAULT_STATE.selectedFilters ],
		seasons: [ ...DEFAULT_STATE.seasons ],
		expeditions: [ ...DEFAULT_STATE.expeditions ],
		adventureOptions: [ ...DEFAULT_STATE.adventureOptions ],
		months: [ ...DEFAULT_STATE.months ],
		durations: [ ...DEFAULT_STATE.durations ],
		ships: [ ...DEFAULT_STATE.ships ],
		perPage: DEFAULT_STATE.perPage,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Set the state.
	setState( updateObject );

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
		pageNumber: updatedPage,
	};

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Set the page to the previous page number.
 */
export const setPreviousPage = () => {
	// Get the state.
	const { pageNumber }: DatesRatesState = getState();

	// Is this the first page?
	if ( pageNumber === 1 ) {
		// There is no previous page.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		pageNumber: pageNumber - 1,
	};

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Set the page to the next page number.
 */
export const setNextPage = () => {
	// Get the state.
	const { pageNumber, totalPages }: DatesRatesState = getState();

	// Is this the last page?
	if ( pageNumber === totalPages ) {
		// There is no next page.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		pageNumber: pageNumber + 1,
	};

	// Set the state.
	setState( updateObject );

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
	if ( Number.isNaN( updatedValue ) || updatedValue < DEFAULT_STATE.perPage ) {
		// No, reject the update.
		return;
	}

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		perPage: updatedValue,
		pageNumber: DEFAULT_STATE.pageNumber,
	};

	// Set the state.
	setState( updateObject );

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
 * @param {number} settings.serverRenderData.pageNumber  The current pageNumber that was rendered on the server.
 * @param {number} settings.serverRenderData.totalPages  The total number of pages.
 * @param {number} settings.serverRenderData.resultCount The total number of results.
 * @param {number} settings.serverRenderData.perPage     The number of results per page.
 */
export const initialize = (
	settings: {
		partial: string,
		selector: string,
		serverRenderData?: {
			pageNumber: number,
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
		updateObject.pageNumber = settings.serverRenderData.pageNumber;
		updateObject.totalPages = settings.serverRenderData.totalPages;
		updateObject.resultCount = settings.serverRenderData.resultCount;
		updateObject.perPage = settings.serverRenderData.perPage;
	}

	// Get the saved state.
	const urlFilters = parseUrl();

	// Null check.
	if ( urlFilters ) {
		// Input containers for filters.
		const filtersInputContainers = {
			seasons: document.getElementById( 'filters-accordion-seasons' ),
			expeditions: document.getElementById( 'filters-accordion-expeditions' ),
			adventureOptions: document.getElementById( 'filters-accordion-adventure-options' ),
			months: document.getElementById( 'filters-accordion-months' ),
			durations: document.getElementById( 'filters-accordion-durations' ),
			ships: document.getElementById( 'filters-accordion-ships' ),
		};

		/**
		 * Our filters are stored as individual lists of @type {DatesRatesFilterState} objects.
		 * We will update the `value` and `label` field based on the values from the `input` elements related to the filter.
		 *
		 * We need to do this because we need the label for each filter value to show in the selected filters. However,
		 * saving the labels in the URL is not a good idea.
		 */

		// Set the selected Filters.
		updateObject.selectedFilters = urlFilters.selectedFilters;

		// Loop through the selectedFilters.
		urlFilters.selectedFilters.forEach( ( selectedFilterKey ) => {
			// Loop and set the filters.
			updateObject[ selectedFilterKey ] = urlFilters[ selectedFilterKey ].map( ( singleFilterValue ) => {
				// Get the corresponding input.
				const correspondingInput = filtersInputContainers[ selectedFilterKey ]?.querySelector( `input[value="${ singleFilterValue }"]` );

				// Return the object.
				return {
					value: singleFilterValue,
					label: correspondingInput?.getAttribute( 'data-label' ) ?? '',
				};
			} ).filter( ( singleFilterValue ) => singleFilterValue.label !== '' );
		} );

		// Other filters.
		if ( ! settings.serverRenderData ) {
			// Set the page related filters.
			updateObject.perPage = urlFilters.perPage;
			updateObject.pageNumber = urlFilters.pageNumber;
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
	const currentState: DatesRatesState = getState();
	const {
		pageNumber,
		partial,
		selector,
		selectedFilters,
		perPage,
		isInitialized,
		isLoading,
	} = currentState;

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
				...Object.fromEntries( selectedFilters.map( ( selectedFilter ) => {
					// Get the snake cased key.
					const snakeCasedKey = camelToSnakeCase( selectedFilter );

					// Get the filter values.
					const filterValues = pluckValues( currentState[ selectedFilter ] );

					// Return the entry.
					return [ snakeCasedKey, filterValues ];
				} ) ),
				posts_per_load: perPage,
				page: pageNumber,
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
	const { perPage, pageNumber, filtersMarkup: filtersMarkupInState }: DatesRatesState = getState();

	// Get the data.
	const {
		markup,
		noResultsMarkup,
		data: { resultCount },
		filtersMarkup,
	} = response;

	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		markup: resultCount !== 0 ? markup : DEFAULT_STATE.markup,
		noResultsMarkup: noResultsMarkup ?? DEFAULT_STATE.noResultsMarkup,
		resultCount,
		totalPages: resultCount !== 0 ? Math.ceil( resultCount / perPage ) : 1,
		pageNumber: resultCount !== 0 ? pageNumber : DEFAULT_STATE.pageNumber,
		shouldMarkupUpdate: true,
		filtersMarkup: filtersMarkup ? filtersMarkup : filtersMarkupInState,
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
		selectedFilters,
		perPage,
		pageNumber,
	}: DatesRatesState = getState();

	// Build url from filters.
	const urlWithParams: string = buildUrlFromFilters( {
		perPage,
		pageNumber,
		selectedFilters,
	} );

	// Update the url with selected/added filters.
	if ( urlWithParams ) {
		updateUrl( urlWithParams );
	}
};

/**
 * Builds the URL from the selected filters.
 *
 * @param {Object}   filters                 The selected filters.
 *
 * @param {string[]} filters.selectedFilters The selected filters.
 * @param {number}   filters.perPage         The items per page.
 * @param {number}   filters.pageNumber      The page number.
 *
 * @return {string} The URL with params.
 */
const buildUrlFromFilters = ( filters: DatesRatesFiltersToUrl ): string => {
	// If queryString not available, early return.
	if ( ! queryString ) {
		// Return early.
		return '';
	}

	// Get current state.
	const currentState: DatesRatesState = getState();
	const { baseUrl, allowedParams } = currentState;

	// Prepare URL params.
	const urlParams: {
		[ key: string ]: string;
	} = {
		// Preserve other params if any.
		...queryString.parse( window.location.search ),
	};

	// Remove filters from the url object. They will be populated again.
	for ( const key of allowedParams ) {
		delete urlParams[ camelToSnakeCase( key ) ];
	}

	// Loop through selected filters and build url params.
	for ( const key in filters ) {
		// Check if it is selectedFilters.
		if ( 'selectedFilters' === key ) {
			// Do nothing.
			continue;
		}

		// Convert camelCased key to snake_caked key.
		const snakeCasedKey: string = camelToSnakeCase( key );

		// @ts-ignore Stringified filters.
		const stringifiedFilter = filters[ key ].toString();

		// @ts-ignore Set the url params value based on key.
		if ( stringifiedFilter && filters[ key ] !== DEFAULT_STATE[ key ] ) {
			urlParams[ snakeCasedKey ] = stringifiedFilter;
		} else {
			delete urlParams[ snakeCasedKey ];
		}
	}

	// Loop through the selected filters.
	filters.selectedFilters.forEach( ( selectedFilter ) => {
		// Convert camelCased key to snake_caked key.
		const snakeCasedKey: string = camelToSnakeCase( selectedFilter );

		// @ts-ignore Stringify the filter and set it in url params.
		urlParams[ snakeCasedKey ] = currentState[ selectedFilter ].map( ( singleFilter ) => singleFilter.value ).toString();

		// Delete if empty.
		if ( urlParams[ snakeCasedKey ].length === 0 ) {
			delete urlParams[ snakeCasedKey ];
		}
	} );

	/**
	 * Return url with params.
	 *
	 * e.g. https://example.com/?expeditions=23,22&seasons=2023-04,2024-05
	 */
	return queryString.stringifyUrl( { url: baseUrl, query: urlParams }, { sort: false } );
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
const parseUrl = (): DatesRatesFiltersFromUrl | null => {
	// If search url or query string not available, return.
	if ( ! window.location.search || ! queryString ) {
		// Early return.
		return null;
	}

	// Get parsed state.
	const parsedState: {
		[ key: string ]: any
	} = convertPropertiesFromSnakeCaseToCamelCase( queryString.parse( window.location.search, { sort: false } ) );

	// Initialize the saved filters object.
	const urlFilters: DatesRatesFiltersFromUrl = {
		selectedFilters: [ ...DEFAULT_STATE.selectedFilters ],
		perPage: DEFAULT_STATE.perPage,
		pageNumber: DEFAULT_STATE.pageNumber,
		seasons: pluckValues( DEFAULT_STATE.seasons ),
		expeditions: pluckValues( DEFAULT_STATE.expeditions ),
		adventureOptions: pluckValues( DEFAULT_STATE.adventureOptions ),
		months: pluckValues( DEFAULT_STATE.months ),
		durations: pluckValues( DEFAULT_STATE.durations ),
		ships: pluckValues( DEFAULT_STATE.ships ),
	};

	// Get allowed params.
	const { allowedParams }: DatesRatesState = getState();

	// Loop through parsed state to build the selected filters from url.
	Object.keys( parsedState ).forEach( ( key: string ) => {
		// Check if the key is amongst the allowed params.
		if ( ! allowedParams.includes( <DatesRatesFilterType>key ) ) {
			// Early return.
			return;
		}

		// Check if it is a numeric key.
		if ( [ 'perPage', 'pageNumber' ].includes( key ) ) {
			let value = parseInt( parsedState[ key ] );

			// @ts-ignore Check if it is a valid number.
			if ( Number.isNaN( value ) || value < DEFAULT_STATE[ key ] ) {
				// @ts-ignore
				value = DEFAULT_STATE[ key ];
			}

			// @ts-ignore
			urlFilters[ key ] = value;
		} else {
			const values = parsedState[ key ]?.split( ',' ).filter( ( v: string ) => v !== '' ) ?? [];

			// Push the item.
			urlFilters.selectedFilters.push( <DatesRatesFilterType>key );

			// @ts-ignore Get and assign the filter values.
			urlFilters[ key ] = values;
		}
	} );

	// Return selected filters state.
	return urlFilters;
};

// Subscribe to the store and update URL on each state update.
subscribe( updateUrlByFilters );
