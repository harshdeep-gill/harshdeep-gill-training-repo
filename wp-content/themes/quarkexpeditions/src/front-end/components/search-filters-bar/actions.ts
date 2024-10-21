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
 * @param {Object[]} destinations Selected Destinations.
 */
export const updateDestinations = ( destinations: SearchFiltersBarDestinationState[] ) => {
	// Get State.
	const { selectedDestinations, selectedMonths }: SearchFiltersBarState = getState();
	let currentSelectedDestinations = [ ...selectedDestinations ];

	// If destinations exist, update the value.
	if ( destinations && Array.isArray( destinations ) && destinations.length ) {
		// Only get the unique destinations.
		currentSelectedDestinations = destinations.reduce( ( accumulator, current ) => {
			// Check if the value is already there.
			if ( ! accumulator.find( ( dest ) => dest.value === current.value ) ) {
				// Add the destination.
				accumulator.push( current );
			}

			// Return
			return accumulator;
		}, <SearchFiltersBarDestinationState[]>[] );
	}

	// Set state.
	setState( {
		selectedDestinations: currentSelectedDestinations,
	} );

	// Fetch Results.
	fetchFilterOptions( 'destinations' );

	// Update Search URL.
	updateSearchUrl(
		currentSelectedDestinations.map( ( dest ) => dest.value ),
		Array.from( selectedMonths ).map( ( month ) => month.value )
	);
};

/**
 * Update months filter value.
 *
 * @param {Object[]} months Selected Months.
 */
export const updateDepartureMonths = ( months: SearchFiltersBarMonthState[] ) => {
	// Get State.
	const { selectedMonths, selectedDestinations }: SearchFiltersBarState = getState();
	let currentSelectedMonths = [ ...selectedMonths ];

	// If months exist, update the value.
	if ( months && Array.isArray( months ) && months.length ) {
		// Only get the unique months values.
		currentSelectedMonths = months.reduce( ( accumulator, current ) => {
			// Check if the value is already there.
			if ( ! accumulator.find( ( month ) => month.value === current.value ) ) {
				// Add the destination.
				accumulator.push( current );
			}

			// Return
			return accumulator;
		}, <SearchFiltersBarMonthState[]>[] );
	} else {
		currentSelectedMonths = [];
	}

	// Set state.
	setState( {
		selectedMonths: currentSelectedMonths,
	} );

	// Fetch Results.
	fetchFilterOptions( 'months' );

	// Update Search URL.
	updateSearchUrl(
		selectedDestinations?.map( ( dest ) => dest.value ),
		currentSelectedMonths?.map( ( month ) => month.value )
	);
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
		const destinations: Array<SearchFiltersBarDestinationState> = Array.from( selectedDestinations );

		// Add the query param.
		updatedApiUrl = `${ filtersApiUrl }?destination_term_id=${ destinations[ 0 ]?.value }`;
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
			console.error(error) //eslint-disable-line
		} );
};

/**
 * Update Search URL with selected filters.
 *
 * @param {Array} destinations Selected Destinations.
 * @param {Array} months       Selected Months.
 */
export const updateSearchUrl = ( destinations: Array<string> = [], months: Array<string> = [] ) => {
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

/**
 * Updates the search history.
 */
export const updateHistory = () => {
	// Get the state.
	const { searchPageUrl, selectedDestinations, selectedMonths, history }: SearchFiltersBarState = getState();

	// Get Construct a url object.
	let baseUrl: URL;

	// Try constructing the url object.
	try {
		baseUrl = new URL( searchPageUrl );
	} catch ( error ) {
		console.error(error); // eslint-disable-line
		return;
	}

	// Get the searchparams object.
	const queryParams = new URLSearchParams( baseUrl.search );
	const destinationsParam = queryParams.get( 'destinations' );
	const monthsParam = queryParams.get( 'months' );

	// Null check.
	if ( ! destinationsParam || ! monthsParam ) {
		// Bail.
		return;
	}

	// Split the params into values.
	const destinationParamValue = destinationsParam.split( ',' )[ 0 ];
	const monthParamValue = monthsParam.split( ',' )[ 0 ];

	// Get the respective objects.
	const destinationObject = selectedDestinations.find( ( selectedDestination ) => selectedDestination.value === destinationParamValue );
	const monthObject = selectedMonths.find( ( selectedMonth ) => selectedMonth.value === monthParamValue );

	// Check if we have destination and month object.
	if ( ! destinationObject || ! monthObject ) {
		// Bail.
		return;
	}

	// Initialize updated history
	const updatedHistory = [ ...history ];

	// Get the index in history.
	const foundIndex = history.findIndex( ( historyItem ) => historyItem.destination.value === destinationObject.value && historyItem.month.value === monthObject.value );

	// Is this item already in history?
	if ( foundIndex > -1 ) {
		// Yes, it is, remove the existing one.
		updatedHistory.splice( foundIndex, 1 );
	}

	// Is the history full already?
	if ( updatedHistory.length === 3 ) {
		// Pop the least recent item.
		updatedHistory.pop();
	}

	// Add the new item.
	updatedHistory.unshift( {
		destination: destinationObject,
		month: monthObject,
	} );

	// Set the state
	setState( {
		history: updatedHistory,
	} );
};
