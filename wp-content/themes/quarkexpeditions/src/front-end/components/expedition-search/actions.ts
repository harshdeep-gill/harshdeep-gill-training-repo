/**
 * External dependencies.
 */
const { zustand, fetchPartial, queryString } = window;

/**
 * Internal dependencies.
 */
const { setState, getState, subscribe } = zustand.stores.expeditionSearch;
import { DEFAULT_STATE } from './data';

/**
 * External dependencies
 */
import { camelToSnakeCase, convertPropertiesFromSnakeCaseToCamelCase } from '../../global/utility';

/**
 * Returns an array for values for a list of filters passed in.
 *
 * @param {Object[]} list
 *
 * @return {string[]|number[]} The array of values.
 */
const pluckValues = ( list: ExpeditionSearchFilterState[] ): string[] => list.map( ( filter ) => filter.value );

/**
 * Parse hash query string for user state.
 *
 * @return {Object} Return selected filters state passed from query string.
 */
const parseUrl = (): ExpeditionSearchFiltersFromUrl | null => {
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
	const urlFilters: ExpeditionSearchFiltersFromUrl = {
		destinations: pluckValues( DEFAULT_STATE.destinations ),
		months: pluckValues( DEFAULT_STATE.months ),
		itineraryLengths: [ ...DEFAULT_STATE.itineraryLengths ],
		ships: pluckValues( DEFAULT_STATE.ships ),
		adventureOptions: pluckValues( DEFAULT_STATE.adventureOptions ),
		languages: pluckValues( DEFAULT_STATE.languages ),
		expeditions: pluckValues( DEFAULT_STATE.expeditions ),
		cabinClasses: pluckValues( DEFAULT_STATE.cabinClasses ),
		travelers: pluckValues( DEFAULT_STATE.travelers ),
	};

	// Get allowed params.
	const { allowedParams }: ExpeditionSearchState = getState();

	// Loop through parsed state to build the selected filters from url.
	Object.keys( parsedState ).forEach( ( key: string ) => {
		// Check if the key is amongst the allowed params.
		if ( ! allowedParams.includes( <ExpeditionSearchFilterType>key ) ) {
			// Early return.
			return;
		}

		// Split values.
		const values = parsedState[ key ].split( ',' ).filter( ( v: string ) => v !== '' ) ?? [];

		// @ts-ignore Get and assign the filter values.
		urlFilters[ key ] = values;
	} );

	// Return selected filters state.
	return urlFilters;
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
 * Build url from selected filters and update url.
 */
const updateUrlByFilters = () => {
	// Build url from filters.
	const urlWithParams: string = buildUrlFromFilters();

	// Update the url with selected/added filters.
	if ( urlWithParams ) {
		updateUrl( urlWithParams );
	}
};

/**
 * Builds the URL from the selected filters.
 *
 * @return {string} The URL with params.
 */
const buildUrlFromFilters = (): string => {
	// If queryString not available, early return.
	if ( ! queryString ) {
		// Return early.
		return '';
	}

	// Get current state.
	const currentState: ExpeditionSearchState = getState();
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

	// Loop through allowed params and build url params.
	allowedParams.forEach( ( selectedFilter ) => {
		// Do we have any selected values?
		if ( currentState[ selectedFilter ].length === 0 ) {
			// Nope, bail.
			return;
		}

		// Convert camelCased key to snake_caked key.
		const snakeCasedKey: string = camelToSnakeCase( selectedFilter );

		// Check if the itinerary lengths are default value. If so, don't store them in the URL.
		if (
			'itineraryLengths' === selectedFilter &&
			currentState.itineraryLengths[ 0 ] === currentState.initialItineraryLengths[ 0 ] &&
			currentState.itineraryLengths[ 1 ] === currentState.initialItineraryLengths[ 1 ]
		) {
			// Delete the data from the url.
			delete urlParams[ snakeCasedKey ];

			// Bail.
			return;
		}

		// Stringify the filter and set it in url params.
		urlParams[ snakeCasedKey ] = currentState[ selectedFilter ].map( ( singleFilter ) => {
			// Check what type we are dealing with.
			if ( singleFilter instanceof Object && 'value' in singleFilter ) {
				// Return the value.
				return singleFilter.value;
			}

			// Return the value.
			return singleFilter;
		} ).toString();

		// Delete if empty.
		if ( urlParams[ snakeCasedKey ].length === 0 ) {
			delete urlParams[ snakeCasedKey ];
		}
	} );

	/**
	 * Return url with params.
	 *
	 * e.g. https://example.com/?expeditions=23,22&destinations=2023-04,2024-05
	 */
	return queryString.stringifyUrl( { url: baseUrl, query: urlParams }, { sort: false } );
};

/**
 * Initialize data for the component.
 *
 * @param {Object} settings                                 Settings.
 * @param {string} settings.partial                         Partial Url.
 * @param {string} settings.selector                        Selector.
 * @param {Object} settings.serverRenderData                Server render data.
 * @param {number} settings.serverRenderData.resultCount    The number of results
 * @param {number} settings.serverRenderData.remainingCount The number of results
 * @param {number} settings.serverRenderData.page           The page number
 * @param {number} settings.serverRenderData.nextPage       The next page number
 */
export const initialize = ( settings: {
	partial: string | undefined,
	selector: string | undefined,
	serverRenderData?: {
		resultCount: number,
		remainingCount: number,
		page: number,
		nextPage: number,
	}
} ): void => {
	// Get current state.
	const currentState: ExpeditionSearchState = getState();
	const selectedFilters: ExpeditionSearchFilters = {
		sort: 'date-now',
	};

	// Initial update object.
	const initialUpdatePayload: ExpeditionsSearchStateUpdateObject = {
		...currentState,
		...settings,
		selectedFilters,
		initialized: true,
		updateMarkup: ! settings.serverRenderData,
		baseUrl: window.location.origin + window.location.pathname,
	};

	// Check if we have server render data.
	if ( settings.serverRenderData ) {
		initialUpdatePayload.resultCount = settings.serverRenderData.resultCount;
		initialUpdatePayload.remainingCount = settings.serverRenderData.remainingCount;
		initialUpdatePayload.page = settings.serverRenderData.page;
		initialUpdatePayload.hasNextPage = settings.serverRenderData.nextPage > settings.serverRenderData.page;
		initialUpdatePayload.nextPage = settings.serverRenderData.nextPage;
	}

	// Get the state from url.
	const urlFilters = parseUrl();

	// Input containers for filters.
	const filtersInputContainers = {
		destinations: document.querySelector( 'quark-expedition-search-filter-destinations' ),
		months: document.querySelector( 'quark-expedition-search-filter-months' ),
		ships: document.querySelector( 'quark-expedition-search-filter-ships' ),
		adventureOptions: document.querySelector( 'quark-expedition-search-filter-adventure-options' ),
		itineraryLengths: document.querySelector( 'quark-expedition-search-filter-itinerary-lengths' ),
		languages: document.querySelector( 'quark-expedition-search-filter-languages' ),
		expeditions: document.querySelector( 'quark-expedition-search-filter-expeditions' ),
		cabinClasses: document.querySelector( 'quark-expedition-search-filter-cabin-classes' ),
		travelers: document.querySelector( 'quark-expedition-search-filter-travelers' ),
	};

	// Null check.
	if ( urlFilters ) {
		/**
		 * Our filters are stored as individual lists of @type {ExpeditionSearchFilterState} objects.
		 * We will update the `value` and `label` field based on the values from the `input` elements related to the filter.
		 *
		 * We need to do this because we need the label for each filter value to show in the selected filters. However,
		 * saving the labels in the URL is not a good idea.
		 */

		// Loop through the url filters.
		for ( const key of currentState.allowedParams ) {
			// Check if it is itinerary length.
			if ( 'itineraryLengths' !== key ) {
				// Loop through the url filters and assign to update object.
				initialUpdatePayload[ key ] = urlFilters[ key ].map( ( singleFilterValue ) => {
					// Get the corresponding input. We convert the key to kebab case here because the input name attributes are in kebab case
					let correspondingInput = filtersInputContainers[ key ]?.querySelector( `input[name="${ camelToSnakeCase( key ).replace( '_', '-' ) }"][value="${ singleFilterValue }"]` );
					let label = correspondingInput?.getAttribute( 'data-label' ) ?? '';

					// Check if this is the months filter
					if ( 'months' === key ) {
						correspondingInput = filtersInputContainers.months?.querySelector( `quark-months-multi-select-option[value="${ singleFilterValue }"]` );
						label = correspondingInput?.getAttribute( 'label' ) ?? '';
					}

					// Return the object.
					return {
						value: singleFilterValue,
						label,
					};
				} ).filter( ( singleFilterValue ) => singleFilterValue.label !== '' );
			} else {
				// @ts-ignore
				initialUpdatePayload[ key ] = urlFilters[ key ].map( ( value ) => {
					// Get the number value.
					const intValue = Number( value );

					// Return number value.
					return Number.isNaN( intValue ) ? 0 : intValue;
				} ).slice( 0, 2 );
			}
		}
	}

	// Get the range slider for it.
	const rangeSlider = filtersInputContainers.itineraryLengths?.querySelector( 'quark-range-slider' );

	// Null check
	if ( rangeSlider ) {
		// Get the values for the initial state or the min/max for this.
		let minValue = parseInt( rangeSlider.getAttribute( 'min' ) ?? '' );
		let maxValue = parseInt( rangeSlider.getAttribute( 'max' ) ?? '' );
		minValue = Number.isNaN( minValue ) ? 0 : minValue;
		maxValue = Number.isNaN( maxValue ) ? 0 : maxValue;

		// Do we have invalid state?
		if (
			! initialUpdatePayload.itineraryLengths ||
			initialUpdatePayload.itineraryLengths.length === 0 ||
			initialUpdatePayload.itineraryLengths[ 0 ] < minValue ||
			initialUpdatePayload.itineraryLengths[ 1 ] > maxValue
		) {
			initialUpdatePayload.itineraryLengths = [ minValue, maxValue ];
		}

		// Set the initial values.
		initialUpdatePayload.initialItineraryLengths = [ minValue, maxValue ];
	}

	// Initialize: Add settings in state.
	setState( initialUpdatePayload );

	// Fetch Results.
	if ( ! settings.serverRenderData ) {
		fetchResults( stateInitialized );
	}
};

/**
 * State Initialized call back.
 *
 * @param {Object} response Response.
 */
const stateInitialized = ( response: PartialData ) => {
	// Get state.
	const { page } = getState();

	// Get response data.
	const {
		markup,
		noResultsMarkup,
		data: { resultCount, nextPage, remainingCount },
	} = response;

	// Set State.
	setState( {
		loading: false,
		markup,
		noResultsMarkup,
		initialized: true,
		resultCount,
		remainingCount,
		hasNextPage: nextPage && nextPage > page,
		nextPage,
	} );
};

/**
 * Update sort filter value.
 *
 * @param {string} sort Selected Sort.
 */
export const updateSort = ( sort: string ) => {
	// Get State.
	const { selectedFilters } = getState();
	const updatedFilters = { ...selectedFilters };

	// If sort exists, update the value.
	if ( sort ) {
		updatedFilters.sort = sort;
	}

	// Set state.
	setState( {
		loading: true,
		selectedFilters: updatedFilters,
		page: 1,
		updateMarkup: true,
	} );

	// Fetch Results.
	fetchResults( filterUpdated );
};

/**
 * Filters updated callback.
 *
 * @param {Object} response Response.
 */
const filterUpdated = ( response: PartialData ) => {
	// Get state.
	const { page, filtersMarkup: filtersMarkupInState } = getState();

	// Get response data.
	const {
		markup,
		data: { resultCount, nextPage, remainingCount },
		noResultsMarkup,
		filtersMarkup,
	} = response;

	// Set state.
	setState( {
		markup: markup ?? '',
		filtersMarkup: filtersMarkup ? filtersMarkup : filtersMarkupInState,
		noResultsMarkup,
		resultCount,
		hasNextPage: nextPage && nextPage > page,
		nextPage,
		updateMarkup: true,
		updateFiltersMarkup: true,
		resetMarkup: false,
		loading: false,
		remainingCount,
	} );
};

/**
 * Fetch Results.
 *
 * @param {Function} callback Callback function.
 */
export const fetchResults = ( callback: Function ) => {
	// Get the current state
	const { loadMoreResults }: ExpeditionSearchState = getState();

	// Set loading: true if not loading more results.
	if ( ! loadMoreResults ) {
		setState( {
			loading: true,
			page: 1, // Important to set this if a filter is changed so that we don't get invalid results
		} );
	}

	// Get data from state.
	const {
		selectedFilters,
		page,
		partial,
		selector,
		destinations,
		months,
		itineraryLengths,
		ships,
		adventureOptions,
		languages,
		expeditions,
		cabinClasses,
		travelers,
	}: ExpeditionSearchState = getState();

	// Fetch partial.
	fetchPartial( partial, {
		selectedFilters: {
			...selectedFilters,
			page,
			destinations: pluckValues( destinations ),
			months: pluckValues( months ),
			itinerary_lengths: itineraryLengths,
			ships: pluckValues( ships ),
			adventure_options: pluckValues( adventureOptions ),
			languages: pluckValues( languages ),
			expeditions: pluckValues( expeditions ),
			cabin_classes: pluckValues( cabinClasses ),
			travelers: pluckValues( travelers ),
		},
	}, callback, selector ).catch( () => {
		// Set state.
		setState( {
			loading: false,
		} );
	} );
};

/**
 * Markup updated callback.
 */
export const markupUpdated = () => {
	// Get the state
	const { loadMoreResults } = getState();

	// Initialize the update object.
	const updatePayload: ExpeditionsSearchStateUpdateObject = {
		loading: false,
		updateMarkup: false,
		resetMarkup: false,
		markup: '',
	};

	// Only unset it if it was set before.
	if ( loadMoreResults ) {
		updatePayload.loadMoreResults = false;
	}

	// Reset all the relevant states, when markup is updated.
	setState( updatePayload );
};

/**
 * Filters updated callback.
 */
export const filtersMarkupUpdated = () => {
	// Initialize the update object.
	const updatePayload: ExpeditionsSearchStateUpdateObject = {
		updateFiltersMarkup: false,
		filtersMarkup: '',
	};

	// Reset all the relevant states, when markup is updated.
	setState( updatePayload );
};

/**
 * Load more results.
 */
export const loadMoreResults = () => {
	// Get state.
	const { hasNextPage, nextPage } = getState();

	// If page no is not available, early return.
	if ( ! hasNextPage || ! nextPage ) {
		// Return.
		return;
	}

	// Set state to load more posts with new page no.
	setState( {
		page: nextPage,
		loadMoreResults: true,
	} );

	// Fetch results.
	fetchResults( moreResultsLoaded );
};

/**
 * More results loaded callback.
 *
 * @param {Object} response Response.
 */
const moreResultsLoaded = ( response: PartialData ) => {
	// Get state.
	const { page } = getState();
	const { markup, data: { nextPage, remainingCount } } = response;

	// Set State.
	setState( {
		markup,
		hasNextPage: nextPage && nextPage > page,
		nextPage,
		updateMarkup: true,
		remainingCount,
		page,
	} );
};

/**
 * Set loading state.
 *
 * @param {boolean} loading Loading state.
 */
export const setLoading = ( loading: boolean ) => {
	// Set loading state.
	setState( { loading } );
};

/**
 * Turn off the show filters state.
 */
export const hideSearchFiltersAction = () => {
	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = { areSearchFiltersShown: false };

	// Hide the filters.
	setState( updateObject );
};

/**
 * Turn on the show filters state.
 */
export const showSearchFiltersAction = () => {
	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = { areSearchFiltersShown: true };

	// Show the filters.
	setState( updateObject );
};

/**
 * Adds a list of destinations to the state.
 *
 * @param { Object[] } destinationsToAdd The list of destinations to add to the state.
 */
export const addDestinations = ( destinationsToAdd: ExpeditionSearchFilterState[] ) => {
	// Get the state.
	const { destinations }: ExpeditionSearchState = getState();

	// Initialize the update Object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {
		destinations: [ ...destinations ],
	};

	// Loop through the valid destinations.
	destinationsToAdd.forEach( ( destinationToAdd ) => {
		// Sanity check.
		if (
			'' === destinationToAdd.value ||
			'' === destinationToAdd.label ||
			( updateObject.destinations && updateObject.destinations?.findIndex( ( existingDestination ) => existingDestination.value === destinationToAdd.value ) > -1 )
		) {
			// Bail.
			return;
		}

		// Remove the children of the current valid destination if any.
		updateObject.destinations = updateObject.destinations?.filter( ( existingDestination ) => existingDestination.parent !== destinationToAdd.value );

		// Push the valid Destination.
		updateObject.destinations?.push( destinationToAdd );
	} );

	// Set the state
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Adds a destination.
 *
 * @param { Object } destinationToAdd the destination object.
 */
export const addDestination = ( destinationToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === destinationToAdd.value || '' === destinationToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { destinations }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( destinations.findIndex( ( existingDestination ) => existingDestination.value === destinationToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Does this destination have a parent?
	if ( ! destinationToAdd.parent ) {
		// Nope, it is the parent destination. Add it after removing its children.
		updateObject.destinations = destinations.filter( ( existingDestination ) => existingDestination.parent !== destinationToAdd.value );
		updateObject.destinations.push( destinationToAdd );
	} else {
		// Just add it normally.
		updateObject.destinations = [ ...destinations, destinationToAdd ];
	}

	// Set the state;
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Removes a destination.
 *
 * @param { string } destinationValue The value of the destination to remove.
 */
export const removeDestination = ( destinationValue: string ) => {
	// Get the state.
	const { destinations }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the destinations.
	updateObject.destinations = destinations.filter( ( existingDestination ) => existingDestination.value !== destinationValue && existingDestination.parent !== destinationValue );

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Adds a ship.
 *
 * @param { Object } shipToAdd the ship object.
 */
export const addShip = ( shipToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === shipToAdd.value || '' === shipToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { ships }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( ships.findIndex( ( existingShip ) => existingShip.value === shipToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the ship.
	updateObject.ships = [ ...ships, shipToAdd ];

	// Set the state;
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Removes a ship.
 *
 * @param { string } shipValue The value of the ship to remove.
 */
export const removeShip = ( shipValue: string ) => {
	// Get the state.
	const { ships }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the ships.
	updateObject.ships = ships.filter( ( existingShip ) => existingShip.value !== shipValue );

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Adds a adventureOption.
 *
 * @param { Object } adventureOptionToAdd the adventureOption object.
 */
export const addAdventureOption = ( adventureOptionToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === adventureOptionToAdd.value || '' === adventureOptionToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { adventureOptions }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( adventureOptions.findIndex( ( existingAdventureOption ) => existingAdventureOption.value === adventureOptionToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the adventureOption.
	updateObject.adventureOptions = [ ...adventureOptions, adventureOptionToAdd ];

	// Set the state;
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Removes a adventureOption.
 *
 * @param { string } adventureOptionValue The value of the adventureOption to remove.
 */
export const removeAdventureOption = ( adventureOptionValue: string ) => {
	// Get the state.
	const { adventureOptions }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the adventureOptions.
	updateObject.adventureOptions = adventureOptions.filter( ( existingAdventureOption ) => existingAdventureOption.value !== adventureOptionValue );

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Adds a language.
 *
 * @param { Object } languageToAdd the language object.
 */
export const addLanguage = ( languageToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === languageToAdd.value || '' === languageToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { languages }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( languages.findIndex( ( existingLanguage ) => existingLanguage.value === languageToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the language.
	updateObject.languages = [ ...languages, languageToAdd ];

	// Set the state;
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Removes a language.
 *
 * @param { string } languageValue The value of the language to remove.
 */
export const removeLanguage = ( languageValue: string ) => {
	// Get the state.
	const { languages }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the languages.
	updateObject.languages = languages.filter( ( existingLanguage ) => existingLanguage.value !== languageValue );

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Adds a expedition.
 *
 * @param { Object } expeditionToAdd the expedition object.
 */
export const addExpedition = ( expeditionToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === expeditionToAdd.value || '' === expeditionToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { expeditions }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( expeditions.findIndex( ( existingExpedition ) => existingExpedition.value === expeditionToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the expedition.
	updateObject.expeditions = [ ...expeditions, expeditionToAdd ];

	// Set the state;
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Removes a expedition.
 *
 * @param { string } expeditionValue The value of the expedition to remove.
 */
export const removeExpedition = ( expeditionValue: string ) => {
	// Get the state.
	const { expeditions }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the expeditions.
	updateObject.expeditions = expeditions.filter( ( existingExpedition ) => existingExpedition.value !== expeditionValue );

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Adds a cabinClass.
 *
 * @param { Object } cabinClassToAdd the cabinClass object.
 */
export const addCabinClass = ( cabinClassToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === cabinClassToAdd.value || '' === cabinClassToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { cabinClasses }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( cabinClasses.findIndex( ( existingCabinClass ) => existingCabinClass.value === cabinClassToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the cabinClass.
	updateObject.cabinClasses = [ ...cabinClasses, cabinClassToAdd ];

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a cabinClass.
 *
 * @param { string } cabinClassValue The value of the cabinClass to remove.
 */
export const removeCabinClass = ( cabinClassValue: string ) => {
	// Get the state.
	const { cabinClasses }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the cabinClasses.
	updateObject.cabinClasses = cabinClasses.filter( ( existingCabinClass ) => existingCabinClass.value !== cabinClassValue );

	// Set the state.
	setState( updateObject );
};

/**
 * Adds a traveler.
 *
 * @param { Object } travelerToAdd the traveler object.
 */
export const addTraveler = ( travelerToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === travelerToAdd.value || '' === travelerToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { travelers }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( travelers.findIndex( ( existingTraveler ) => existingTraveler.value === travelerToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the traveler.
	updateObject.travelers = [ ...travelers, travelerToAdd ];

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a traveler.
 *
 * @param { string } travelerValue The value of the traveler to remove.
 */
export const removeTraveler = ( travelerValue: string ) => {
	// Get the state.
	const { travelers }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the travelers.
	updateObject.travelers = travelers.filter( ( existingTraveler ) => existingTraveler.value !== travelerValue );

	// Set the state.
	setState( updateObject );
};

/**
 * Adds a month.
 *
 * @param { Object } monthToAdd the month object.
 */
export const addMonth = ( monthToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === monthToAdd.value || '' === monthToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { months }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( months.findIndex( ( existingMonth ) => existingMonth.value === monthToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the month.
	updateObject.months = [ ...months, monthToAdd ];

	// Set the state;
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Updates the months
 *
 * @param { Object[] } monthsToAdd Array of month objects.
 */
export const updateMonths = ( monthsToAdd: ExpeditionSearchFilterState[] ) => {
	// Get the current months state.
	const { months }: ExpeditionSearchState = getState();
	const updateObject: ExpeditionsSearchStateUpdateObject = {
		months: [ ...months ],
	};

	// Sanity checks
	if ( monthsToAdd && Array.isArray( monthsToAdd ) ) {
		// Only get the unique months values.
		updateObject.months = monthsToAdd.reduce( ( accumulator, current ) => {
			// Check if the value is already there.
			if ( ! accumulator.find( ( month ) => month.value === current.value ) ) {
				// Add the destination.
				accumulator.push( current );
			}

			// Return
			return accumulator;
		}, <SearchFiltersBarMonthState[]>[] );
	}

	// Set the state
	setState( updateObject );

	// Fetch the results
	fetchResults( filterUpdated );
};

/**
 * Removes a month.
 *
 * @param { string } monthValue The value of the month to remove.
 */
export const removeMonth = ( monthValue: string ) => {
	// Get the state.
	const { months }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the months.
	updateObject.months = months.filter( ( existingMonth ) => existingMonth.value !== monthValue );

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Updates the state based on history.
 *
 * @param { Object } destination The destination object.
 * @param { Object } month       The month object.
 */
export const setStateFromHistory = ( destination: ExpeditionSearchFilterState, month: ExpeditionSearchFilterState ) => {
	// Check if we should proceed.
	if ( ! (
		destination &&
		destination.value &&
		destination.label &&
		month &&
		month.value &&
		month.label
	) ) {
		// Bail.
		return;
	}

	// Get the state.
	const { initialItineraryLengths }: ExpeditionSearchState = getState();

	// Prepare the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {
		destinations: [ destination ],
		months: [ month ],
		itineraryLengths: [ ...initialItineraryLengths ],
		ships: [ ...DEFAULT_STATE.ships ],
		adventureOptions: [ ...DEFAULT_STATE.adventureOptions ],
		languages: [ ...DEFAULT_STATE.languages ],
		expeditions: [ ...DEFAULT_STATE.expeditions ],
		cabinClasses: [ ...DEFAULT_STATE.cabinClasses ],
		travelers: [ ...DEFAULT_STATE.travelers ],
	};

	// Set the state
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Removes the filters from a history card from history.
 *
 * @param { string } destinationValue The destination value
 * @param { string } monthValue       The month value
 */
export const removeHistoryFromFilters = ( destinationValue: string, monthValue: string ) => {
	// Check if we can proceed
	if ( ! destinationValue || ! monthValue ) {
		// Bail.
		return;
	}

	// Get the state
	const { destinations, months }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {
		destinations: destinations.filter( ( dest ) => dest.value !== destinationValue && dest.parent !== destinationValue ),
		months: months.filter( ( month ) => month.value !== monthValue ),
	};

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Updates the itineraryLength.
 *
 * @param { [ number, number ] } updatedItineraryLengths the itineraryLength object.
 */
export const updateItineraryLength = ( updatedItineraryLengths: [ number, number ] ) => {
	// Sanity check.
	if (
		! Array.isArray( updatedItineraryLengths ) ||
		updatedItineraryLengths.length !== 2 ||
		Number.isNaN( updatedItineraryLengths[ 0 ] ) ||
		Number.isNaN( updatedItineraryLengths[ 1 ] ) ||
		0 > updatedItineraryLengths[ 0 ] ||
		updatedItineraryLengths[ 0 ] > updatedItineraryLengths[ 1 ]
	) {
		// Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Update the itineraryLength.
	updateObject.itineraryLengths = [ ...updatedItineraryLengths ];

	// Update the state;
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

/**
 * Clears all the filters.
 */
export const clearAllFilters = () => {
	// Get the state.
	const { initialItineraryLengths }: ExpeditionSearchState = getState();
	// Prepare the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {
		destinations: [ ...DEFAULT_STATE.destinations ],
		months: [ ...DEFAULT_STATE.months ],
		itineraryLengths: [ ...initialItineraryLengths ],
		ships: [ ...DEFAULT_STATE.ships ],
		adventureOptions: [ ...DEFAULT_STATE.adventureOptions ],
		languages: [ ...DEFAULT_STATE.languages ],
		expeditions: [ ...DEFAULT_STATE.expeditions ],
		cabinClasses: [ ...DEFAULT_STATE.cabinClasses ],
		travelers: [ ...DEFAULT_STATE.travelers ],
	};

	// Set the state.
	setState( updateObject );

	// Fetch the results.
	fetchResults( filterUpdated );
};

// Subscribe to the store and update URL on each state update.
subscribe( updateUrlByFilters );
