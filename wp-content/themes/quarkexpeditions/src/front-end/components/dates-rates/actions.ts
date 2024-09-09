/**
 * Globals.
 */
const { zustand, fetchPartial } = window;

/**
 * Internal dependencies.
 */
const { setState, getState } = zustand.stores.datesRates;

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
		areCurrencyFiltersSyncing: true,
	} );

	// Disable syncing status.
	setState( {
		areCurrencyFiltersSyncing: false,
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
	} );
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
	} );
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
	} );
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
	} );
};

/**
 * Adds a adventureOption to the adventureOptions list.
 *
 * @param {Object} filter A singular filter.
 */
export const addAdventureOption = ( filter: DatesRatesFilterState ) => {
	// Get the state.
	const { adventure_options: adventureOptions }: DatesRatesState = getState();

	// Check if the given filter is already selected.
	if ( adventureOptions.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
		// It is, bail.
		return;
	}

	// Set the state
	setState( {
		adventure_options: [ ...adventureOptions, filter ],
	} );
};

/**
 * Remove a particular adventureOption given its value.
 *
 * @param {string} filterValue
 */
export const removeAdventureOption = ( filterValue: string ) => {
	// Get the state.
	const { adventure_options: adventureOptions }: DatesRatesState = getState();

	// Set the state.
	setState( {
		adventure_options: adventureOptions.filter( ( existingFilter ) => existingFilter.value !== filterValue ),
	} );
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
	} );
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
	} );
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
	} );
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
	} );
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
	} );
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
	} );
};

/**
 * Clears all the filters.
 */
export const clearAllFilters = () => {
	// Set the state.
	setState( {
		seasons: [],
		expeditions: [],
		adventure_options: [],
		months: [],
		durations: [],
		ships: [],
	} );
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
};

/**
 * Sets the totalPages state.
 *
 * @param {number} updatedValue
 */
export const setTotalPages = ( updatedValue: number ) => {
	// Is this a valid updated totalPages?
	if ( Number.isNaN( updatedValue ) || updatedValue < 1 ) {
		// No, reject the update.
		return;
	}

	// Set the state
	setState( {
		totalPages: updatedValue,
	} );
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
	} );
};

/**
 * Set the total items count.
 *
 * @param {number} updatedValue The updated value.
 */
export const setTotalCount = ( updatedValue: number ) => {
	// Is this a valid updated totalPages?
	if ( Number.isNaN( updatedValue ) || updatedValue < 0 ) {
		// No, reject the update.
		return;
	}

	// Set the state
	setState( {
		totalItems: updatedValue,
	} );
};

/**
 * Initializes the settings to be used for fetching the results.
 *
 * @param {Object} settings              The settings.
 * @param {string} settings.partial      The name of the partial.
 * @param {string} settings.selector     The DOM selector of the container of the results.
 * @param {number} settings.expeditionId The expeditionId to be used.
 */
export const initializeFetchPartialSettings = (
	settings: {
		partial: string,
		selector: string,
		expeditionId: number
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
		settings.selector &&
		! Number.isNaN( settings.expeditionId )
	) ) {
		// Invalid data.
		return;
	}

	// Set the state.
	setState( {
		partial: settings.partial,
		selector: settings.selector,
		expeditionId: settings.expeditionId,
		isInitialized: true,
		shouldMarkupUpdate: true,
	} );

	// Fetch the results.
	fetchResults();
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
		adventure_options: adventureOptions,
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
	).catch( () => setState( { isLoading: false } ) );
};

/**
 * Returns an array for values for a list of filters passed in.
 *
 * @param {Object[]} list
 *
 * @return {string[]|number[]} The array of values.
 */
const pluckValues = ( list: DatesRatesFilterState[] ) => list.map( ( filter ) => filter.value );

/**
 * Callback to run after partial has been fetched.
 *
 * @param {Object} data The partial data.
 */
const resultsFetchedCallback = ( data: PartialData ) => {
	// Set state.
	setState( {
		isLoading: false,
	} );
};
