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
 * @param {Object} options                       Options.
 * @param {Array}  options.departureMonthOptions Departure Month Options
 * @param {Array}  options.destinationOptions    Destination Options
 * @param {Object} settings                      Settings
 * @param {string} settings.filtersApiUrl        Filters API URL
 */
export const initialize = ( options: {
	departureMonthOptions: [],
	destinationOptions: [],
}, settings: { filtersApiUrl: string | null } ): void => {
	// Get current state.
	const currentState: SearchFiltersBarState = getState();

	// Initialize: Add initial options in state.
	setState( {
		...currentState,
		...options,
		...settings,
		initialized: true,
	} );

	// Fetch Results.
	fetchFilterOptions();
};

/**
 * Update destination filter value.
 *
 * @param {Set} destinations Selected Destinations.
 */
export const updateDestinations = ( destinations: Set<string> | undefined ) => {
	// Get State.
	const { selectedFilters } = getState();
	const updatedFilters = { ...selectedFilters };

	// If destinations exist, update the value.
	if ( destinations ) {
		updatedFilters.destinations = destinations;
	}

	// Set state.
	setState( {
		selectedFilters: updatedFilters,
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
	const { selectedFilters } = getState();
	const updatedFilters = { ...selectedFilters };

	// If months exist, update the value.
	if ( months ) {
		updatedFilters.departureMonths = months;
	}

	// Set state.
	setState( {
		selectedFilters: updatedFilters,
	} );

	// Fetch Results.
	fetchFilterOptions();
};

/**
 * Fetch Filter Options.
 */
export const fetchFilterOptions = () => {
	// Get data from state.
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
