/**
 * External dependencies.
 */
const { zustand } = window;

/**
 * Internal dependencies.
 */
const { setState, getState } = zustand.stores.searchFiltersBar;

/**
 * Initialize data for the component.
 *
 * @param {Object} settings               Settings
 * @param {string} settings.filtersApiUrl Filters API URL
 * @param {string} settings.searchPageUrl Search Page URL
 */
export const initialize = ( settings: { filtersApiUrl: string | null, searchPageUrl: string | null } ): void => {
	// Get current state.
	const currentState: SearchFiltersBarState = getState();

	// Initialize: Add initial options in state.
	setState( {
		...currentState,
		...settings,
		initialized: true,
	} );
};

/**
 * Update destination filter value.
 *
 * @param {Set} destinations Selected Destinations.
 */
export const updateDestinations = ( destinations: Set<string> | undefined ) => {
	// Get State.
	const { selectedDestinations, selectedMonths } = getState();
	let currentSelectedDestinations = { ...selectedDestinations };

	// If destinations exist, update the value.
	if ( destinations ) {
		currentSelectedDestinations = destinations;
	}

	// Set state.
	setState( {
		selectedDestinations: currentSelectedDestinations,
	} );

	// Fetch Results.
	fetchFilterOptions();

	// Update Search URL.
	updateSearchUrl( Array.from( currentSelectedDestinations ), Array.from( selectedMonths ) );
};

/**
 * Update months filter value.
 *
 * @param {Set} months Selected Months.
 */
export const updateDepartureMonths = ( months: Set<string> | undefined ) => {
	// Get State.
	const { selectedMonths, selectedDestinations } = getState();
	let currentSelectedMonths = { ...selectedMonths };

	// If months exist, update the value.
	if ( months ) {
		currentSelectedMonths = months;
	}

	// Set state.
	setState( {
		selectedMonths: currentSelectedMonths,
	} );

	// Fetch Results.
	fetchFilterOptions( 'months' );

	// Update Search URL.
	updateSearchUrl( Array.from( selectedDestinations ), Array.from( currentSelectedMonths ) );
};

/**
 * Fetch relevant filter options.
 *
 * @param {string} type Request Type.
 */
export const fetchFilterOptions = ( type: string = 'destinations' ) => {
	// Get State.
	const { filtersApiUrl, selectedDestinations, selectedMonths } = getState();
	let updatedApiUrl = filtersApiUrl;

	// Check if the url exists.
	if ( ! filtersApiUrl ) {
		// Bail.
		return;
	}

	// If request type is destinations, then update the api url with the query param.
	if ( 'destinations' === type && selectedDestinations ) {
		// Get the first selected destination term.
		const destinations = Array.from( selectedDestinations );

		// Add the query param.
		updatedApiUrl = `${ filtersApiUrl }?destination_term_id=${ destinations[ 0 ] }`;
	}

	// If request type is months, then update the api url with the query param.
	if ( 'months' === type && selectedMonths ) {
		// Get the first selected month value.
		const months = Array.from( selectedMonths );

		// Add the query param.
		updatedApiUrl = `${ filtersApiUrl }?month=${ months[ 0 ] }`;
	}

	// Fetch relevant filter options.
	fetch( updatedApiUrl, {
		method: 'GET',
	} )
		.then( ( response ) => {
			// Return response.
			return response.json();
		} )
		.then( ( data ) => {
			// Set the relevant state to refresh the filter options.
			if ( 'destinations' === type ) {
				setState( {
					departureMonthOptions: data?.months,
				} );
			} else {
				setState( {
					destinationOptions: data?.destinations,
				} );
			}
		} )
		.catch( ( error ) => {
			// Handle any errors that occur during the fetch.
			console.error( error ) //eslint-disable-line
		} );
};

/**
 * Update Search URL with selected filters.
 *
 * @param {Array} destinations Selected Destinations.
 * @param {Array} months       Selected Months.
 */
export const updateSearchUrl = ( destinations: Array<number> = [], months: Array<number> = [] ) => {
	// Check if either destination or month exist.
	if ( ! destinations.length && ! months.length ) {
		// Bail.
		return;
	}

	// Get State.
	const { searchPageUrl } = getState();

	// Get the Base URL and params.
	const baseUrl = new URL( searchPageUrl );
	const queryParams = new URLSearchParams( baseUrl.search );

	// Set the params.
	queryParams.set( 'destinations', destinations.toString() );
	queryParams.set( 'months', months.toString() );

	// Set the search query params for the URL.
	baseUrl.search = queryParams.toString();

	// Set State.
	setState( {
		searchPageUrl: baseUrl.href,
	} );
};
