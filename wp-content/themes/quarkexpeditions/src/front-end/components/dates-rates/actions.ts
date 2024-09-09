/**
 * External dependencies.
 */
const { zustand } = window;

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

	//TODO: fetch results
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
