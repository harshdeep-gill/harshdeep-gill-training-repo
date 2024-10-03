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
 */
export const initialize = ( settings: { filtersApiUrl: string | null } ): void => {
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
	const { selectedDestinations } = getState();
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
};

/**
 * Update months filter value.
 *
 * @param {Set} months Selected Months.
 */
export const updateDepartureMonths = ( months: Set<string> | undefined ) => {
	// Get State.
	const { selectedMonths } = getState();
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
 * Set loading state.
 *
 * @param {boolean} loading Loading state.
 */
export const setLoading = ( loading: boolean ) => {
	// Set loading state.
	setState( { loading } );
};
