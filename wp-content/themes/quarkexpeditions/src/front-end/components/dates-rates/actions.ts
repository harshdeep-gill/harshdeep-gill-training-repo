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
 * The name of the filter for which to create an `add` action.
 *
 * @param {Object} filterName
 *
 * @return {Function} An add filter action.
 */
const addFilterCreator = ( filterName: string ) => {
	/**
	 * Adds a filter to the filter list.
	 *
	 * @param {Object} filter A singular filter.
	 */
	return ( filter: DatesRatesFilterState ) => {
		// Get the state.
		const filterList: DatesRatesFilterState[] = getState()[ filterName ];

		// Check if the given filter is already selected.
		if ( filterList.find( ( existingFilter ) => existingFilter.value === filter.value ) ) {
			// It is, bail.
			return;
		}

		// Create an update object.
		const updatedObject: { [key: string]: DatesRatesFilterState[] } = {};
		updatedObject[ filterName ] = [ ...filterList, filter ];

		// Set the state
		setState( updatedObject );
	};
};

/**
 * The name of the filter for which to create an `remove` action.
 *
 * @param {Object} filterName
 *
 * @return {Function} An add filter action.
 */
export const removeFilterCreator = ( filterName: string ) => {
	/**
	 * Remove a particular filter given its value.
	 *
	 * @param {string} filterValue
	 */
	return ( filterValue: string ) => {
		// Get the state.
		const filterList: DatesRatesFilterState[] = getState()[ filterName ];

		// Create an update object.
		const updatedObject: { [key: string]: DatesRatesFilterState[] } = {};
		updatedObject[ filterName ] = filterList.filter( ( existingFilter ) => existingFilter.value !== filterValue );

		// Set the state.
		setState( updatedObject );
	};
};

// seasons actions.
export const addSeason = addFilterCreator( 'seasons' );
export const removeSeason = removeFilterCreator( 'seasons' );

// expeditions actions.
export const addExpedition = addFilterCreator( 'expeditions' );
export const removeExpedition = removeFilterCreator( 'expeditions' );

// adventure_options actions.
export const addAdventureOption = addFilterCreator( 'adventure_options' );
export const removeAdventureOption = removeFilterCreator( 'adventure_options' );

// months actions.
export const addDepartureMonth = addFilterCreator( 'months' );
export const removeDepartureMonth = removeFilterCreator( 'months' );

// durations actions.
export const addDuration = addFilterCreator( 'durations' );
export const removeDuration = removeFilterCreator( 'durations' );

// ships actions.
export const addShip = addFilterCreator( 'ships' );
export const removeShip = removeFilterCreator( 'ships' );

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
