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
 * Update currency filter value.
 *
 * TODO: Remove this when the global currency switcher is available.
 *
 * @param {string} updatedCurrency Selected Currency.
 */
export const updateCurrency = ( updatedCurrency: string ) => {
	// Get State.
	if ( ! updatedCurrency ) {
		updatedCurrency = DEFAULT_STATE.currency;
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
 * Adds a filter value to the selected filters.
 *
 * @param {Object} filterToAdd The filter to update.
 */
const addSelectedFilter = ( filterToAdd: DatesRatesFilterStateUpdateObject ) => {
	// Get the state.
	const { selectedFilters }: DatesRatesState = getState();

	// Find the filter state.
	const isTheFilterTypeSelected = selectedFilters.findIndex( ( selectedFilter ) => selectedFilter.type === filterToAdd.type ) > -1;

	// Initialize the update object.
	const updateObject: DatesRatesStateUpdateObject = {
		selectedFilters: [ ...selectedFilters ],
		page: DEFAULT_STATE.page,
	};

	// Null check.
	if ( ! updateObject.selectedFilters ) {
		updateObject.selectedFilters = [];
	}

	// Check if the filter type is selected.
	if ( ! isTheFilterTypeSelected ) {
		updateObject.selectedFilters.push( { type: filterToAdd.type, filters: [ filterToAdd.filter ] } );
	} else {
		// New Selected filters.
		updateObject.selectedFilters = [
			...( updateObject.selectedFilters ).map(
				( selectedFilter ): DatesRatesFilterState => {
					// Check if we should add the value or not.
					if (
						selectedFilter.type === filterToAdd.type &&
						! selectedFilter.filters.find( ( filterVal ) => filterVal.value === filterToAdd.filter.value )
					) {
						// Return the updated filter.
						return {
							type: selectedFilter.type,
							filters: [ ...selectedFilter.filters, filterToAdd.filter ],
						};
					}

					// The existing value.
					return selectedFilter;
				}
			),
		];
	}

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Removes a selected filter.
 *
 * @param {string} filterTypeToRemove  The filter to remove.
 * @param {string} filterValueToRemove The value to match.
 */
const removeSelectedFilter = ( filterTypeToRemove: DatesRatesFilterType, filterValueToRemove: string ) => {
	// Get the state.
	const { selectedFilters }: DatesRatesState = getState();

	// Initialize update Object.
	const updateObject: DatesRatesStateUpdateObject = {
		selectedFilters: selectedFilters.map( ( selectedFilter ) => {
			// Check if this is the target?
			if ( selectedFilter.type !== filterTypeToRemove ) {
				// Nope.
				return selectedFilter;
			}

			// Remove the filter.
			return {
				type: selectedFilter.type,
				filters: selectedFilter.filters.filter( ( singleSelectedFilter ) => singleSelectedFilter.value !== filterValueToRemove ),
			};
		} ).filter( ( selectedFilter ) => selectedFilter.filters.length > 0 ),
		page: DEFAULT_STATE.page,
	};

	// Set the state.
	setState( updateObject );

	// Fetch results.
	fetchResults();
};

/**
 * Gets the selected seasons values.
 *
 * @return {Object[]} The filter values.
 */
export const getSeasonsState = () => {
	// Get the selectedFilters.
	const { selectedFilters }: DatesRatesState = getState();

	// Find and return the filter values.
	return selectedFilters.find( ( selectedFilter ) => selectedFilter.type === 'seasons' )?.filters ?? [];
};

/**
 * Adds a season to the seasons list.
 *
 * @param {Object} filter A singular filter.
 */
export const addSeason = ( filter: DatesRatesSelectedFilter ) => addSelectedFilter( { type: 'seasons', filter } );

/**
 * Remove a particular season given its value.
 *
 * @param {string} filterValue
 */
export const removeSeason = ( filterValue: string ) => removeSelectedFilter( 'seasons', filterValue );

/**
 * Gets the selected expeditions values.
 *
 * @return {Object[]} The filter values.
 */
export const getExpeditionsState = () => {
	// Get the selectedFilters.
	const { selectedFilters }: DatesRatesState = getState();

	// Find and return the filter values.
	return selectedFilters.find( ( selectedFilter ) => selectedFilter.type === 'expeditions' )?.filters ?? [];
};

/**
 * Adds a expedition to the expeditions list.
 *
 * @param {Object} filter A singular filter.
 */
export const addExpedition = ( filter: DatesRatesSelectedFilter ) => addSelectedFilter( { type: 'expeditions', filter } );

/**
 * Remove a particular expedition given its value.
 *
 * @param {string} filterValue
 */
export const removeExpedition = ( filterValue: string ) => removeSelectedFilter( 'expeditions', filterValue );

/**
 * Gets the selected adventureOptions values.
 *
 * @return {Object[]} The filter values.
 */
export const getAdventureOptionsState = () => {
	// Get the selectedFilters.
	const { selectedFilters }: DatesRatesState = getState();

	// Find and return the filter values.
	return selectedFilters.find( ( selectedFilter ) => selectedFilter.type === 'adventureOptions' )?.filters ?? [];
};

/**
 * Adds a adventureOption to the adventureOptions list.
 *
 * @param {Object} filter A singular filter.
 */
export const addAdventureOption = ( filter: DatesRatesSelectedFilter ) => addSelectedFilter( { type: 'adventureOptions', filter } );

/**
 * Remove a particular adventureOption given its value.
 *
 * @param {string} filterValue
 */
export const removeAdventureOption = ( filterValue: string ) => removeSelectedFilter( 'adventureOptions', filterValue );

/**
 * Gets the selected months values.
 *
 * @return {Object[]} The filter values.
 */
export const getDepartureMonthsState = () => {
	// Get the selectedFilters.
	const { selectedFilters }: DatesRatesState = getState();

	// Find and return the filter values.
	return selectedFilters.find( ( selectedFilter ) => selectedFilter.type === 'months' )?.filters ?? [];
};

/**
 * Adds a month to the months list.
 *
 * @param {Object} filter A singular filter.
 */
export const addDepartureMonth = ( filter: DatesRatesSelectedFilter ) => addSelectedFilter( { type: 'months', filter } );

/**
 * Remove a particular month given its value.
 *
 * @param {string} filterValue
 */
export const removeDepartureMonth = ( filterValue: string ) => removeSelectedFilter( 'months', filterValue );

/**
 * Gets the selected durations values.
 *
 * @return {Object[]} The filter values.
 */
export const getDurationsState = () => {
	// Get the selectedFilters.
	const { selectedFilters }: DatesRatesState = getState();

	// Find and return the filter values.
	return selectedFilters.find( ( selectedFilter ) => selectedFilter.type === 'durations' )?.filters ?? [];
};

/**
 * Adds a duration to the durations list.
 *
 * @param {Object} filter A singular filter.
 */
export const addDuration = ( filter: DatesRatesSelectedFilter ) => addSelectedFilter( { type: 'durations', filter } );

/**
 * Remove a particular duration given its value.
 *
 * @param {string} filterValue
 */
export const removeDuration = ( filterValue: string ) => removeSelectedFilter( 'durations', filterValue );

/**
 * Gets the selected ships values.
 *
 * @return {Object[]} The filter values.
 */
export const getShipsState = () => {
	// Get the selectedFilters.
	const { selectedFilters }: DatesRatesState = getState();

	// Find and return the filter values.
	return selectedFilters.find( ( selectedFilter ) => selectedFilter.type === 'ships' )?.filters ?? [];
};

/**
 * Adds a ship to the ships list.
 *
 * @param {Object} filter A singular filter.
 */
export const addShip = ( filter: DatesRatesSelectedFilter ) => addSelectedFilter( { type: 'ships', filter } );

/**
 * Remove a particular ship given its value.
 *
 * @param {string} filterValue
 */
export const removeShip = ( filterValue: string ) => removeSelectedFilter( 'ships', filterValue );

/**
 * Clears all the filters.
 */
export const clearAllFilters = () => {
	// Update object.
	const updateObject: DatesRatesStateUpdateObject = {
		selectedFilters: DEFAULT_STATE.selectedFilters,
		perPage: DEFAULT_STATE.perPage,
		page: DEFAULT_STATE.page,
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
		page: updatedPage,
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
		page: DEFAULT_STATE.page,
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
		 * Our filters are stored as individual lists of @type {DatesRatesSelectedFilter} objects.
		 * We will update the `value` and `label` field based on the values from the `input` elements related to the filter.
		 *
		 * We need to do this because we need the label for each filter value to show in the selected filters. However,
		 * saving the labels in the URL is not a good idea.
		 */
		updateObject.selectedFilters = urlFilters.selectedFilters.map( ( selectedFilter ) => {
			// Get the type of filter.
			const currentFilterType = selectedFilter.type;

			// Return the selectedFilter item.
			return {
				type: currentFilterType,
				filters: selectedFilter.filters.map(
					( singleFilter ) => {
						// Get the input.
						const theCorrespondingInput = filtersInputContainers[ currentFilterType ]?.querySelector( `input[value="${ singleFilter.value }"]` );

						// Return the filter.
						return {
							value: singleFilter.value,
							label: theCorrespondingInput?.getAttribute( 'data-label' ) ?? '',
						};
					}
				).filter( ( singleFilter ) => singleFilter.label !== '' ),
			};
		} );

		// Other filters.
		if ( ! settings.serverRenderData ) {
			// Set the page related filters.
			updateObject.perPage = urlFilters.perPage;
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
		selectedFilters,
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
const pluckValues = ( list: DatesRatesSelectedFilter[] ): string[] => list.map( ( filter ) => filter.value );

/**
 * Callback to run after partial has been fetched.
 *
 * @param {Object} response The partial response.
 */
const resultsFetchedCallback = ( response: PartialData ) => {
	// Get state.
	const { perPage, page, filtersMarkup: filtersMarkupInState }: DatesRatesState = getState();

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
		page: resultCount !== 0 ? page : DEFAULT_STATE.page,
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
	}: DatesRatesState = getState();

	// Build url from filters.
	const urlWithParams: string = buildUrlFromFilters( {
		selectedFilters,
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
 * @param {Object}   filters                 The selected filters.
 *
 * @param {Object[]} filters.selectedFilters The selected filters.
 * @param {number}   filters.perPage         The items per page.
 *
 * @return {string} The URL with params.
 */
const buildUrlFromFilters = ( filters: DatesRatesFiltersInUrl ): string => {
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
		// Check if it is selectedFilters.
		if ( 'selectedFilters' === key ) {
			// Do nothing.
			continue;
		}

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

	// Loop through the selected filters.
	filters.selectedFilters.forEach( ( selectedFilter ) => {
		// Convert camelCased key to snake_caked key.
		const snakeCasedKey: string = camelToSnakeCase( selectedFilter.type );

		// Stringify the filter and set it in url params.
		urlParams[ snakeCasedKey ] = selectedFilter.filters.map( ( singleFilter ) => singleFilter.value ).toString();

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
	return queryString.stringifyUrl(
		{
			url: baseUrl,
			query: urlParams,
		},
		{
			sort: ( a: DatesRatesFilterState, b: DatesRatesFilterState ): number => {
				// Ascending order.
				return (
					filters.selectedFilters.findIndex(
						( selectedFilter ) => selectedFilter.type === a.type
					) - filters.selectedFilters.findIndex(
						( selectedFilter ) => selectedFilter.type === b.type
					)
				);
			},
		}
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
const parseUrl = (): DatesRatesFiltersInUrl | null => {
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
	const urlFilters: DatesRatesFiltersInUrl = {
		selectedFilters: DEFAULT_STATE.selectedFilters,
		perPage: DEFAULT_STATE.perPage,
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

		// Check if it is a numeric key.
		if ( 'perPage' === key ) {
			let value = parseInt( parsedState[ key ] );

			// Check if it is a valid number.
			if ( Number.isNaN( value ) || value < DEFAULT_STATE.perPage ) {
				value = DEFAULT_STATE.perPage;
			}

			// @ts-ignore
			urlFilters[ key ] = value;
		} else {
			const values = parsedState[ key ]?.split( ',' ).filter( ( v: string ) => v !== '' ) ?? [];

			// @ts-ignore.
			urlFilters.selectedFilters.push( { type: key, filters: values.foreach( ( v: string ) => ( { label: '', value: v } ) ) } );
		}
	} );

	// Return selected filters state.
	return urlFilters;
};

// Subscribe to the store and update URL on each state update.
subscribe( updateUrlByFilters );
