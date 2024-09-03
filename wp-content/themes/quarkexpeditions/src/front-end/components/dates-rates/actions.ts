/**
 * External dependencies.
 */
const { zustand, fetchPartial } = window;

/**
 * Internal dependencies.
 */
const { setState, getState } = zustand.stores.datesRates;

/**
 * Update currency filter value.
 *
 * @param {string} currency Selected Currency.
 */
export const updateCurrency = ( currency: string ) => {
	// Get State.
	const { selectedFilters } = getState();
	const updatedFilters: DatesRatesFilters = { ...selectedFilters };

	// If currency exists, update the value.
	if ( currency ) {
		updatedFilters.currency = currency;
	}

	// Set state.
	setState( {
		loading: true,
		selectedFilters: updatedFilters,
		page: 1,
		updateMarkup: true,
	} );

	// Fetch Results.

	//TODO: fetchResults( filterUpdated );
};

/**
 * Update filter values.
 *
 * @param {Object} filterValues Selected filters.
 */
export const updateFilters = ( filterValues: DatesRatesFilters ) => {
	// Get State.
	const { selectedFilters } = getState();
	const updatedFilters: DatesRatesFilters = { ...selectedFilters, ...filterValues };

	// Set state.
	setState( {
		loading: true,
		selectedFilters: updatedFilters,
		page: 1,
		updateMarkup: true,
	} );

	// Fetch Results.

	//TODO: fetchResults( filterUpdated );
};

/**
 * Fetch Results.
 *
 * @param {Function} callback Callback function.
 */
export const fetchResults = ( callback: Function ) => {
	// Get data from state.
	const { selectedFilters, page, partial, selector, expeditionId }: DatesRatesState = getState();

	// Set loading: true.
	setState( {
		loading: true,
	} );

	// Fetch partial.
	fetchPartial( partial, {
		selectedFilters: {
			...selectedFilters,
			page,
			expeditions: [ expeditionId ],
		},
	}, callback, selector ).catch( () => {
		// Set state.
		setState( {
			loading: false,
		} );
	} );
};

/**
 * Filters updated callback.
 *
 * @param {Object} response Response.
 */
export const filterUpdated = ( response: PartialData ) => {
	// Get state.
	const { page } = getState();

	// Get response data.
	const {
		markup,
		data: { resultCount, nextPage },
		noResultsMarkup,
	} = response;

	// Set state.
	setState( {
		markup: 0 !== resultCount ? markup : '',
		noResultsMarkup,
		resultCount,
		hasNextPage: nextPage && nextPage > page,
		nextPage,
		updateMarkup: true,
		resetMarkup: false,
		loading: false,
	} );
};
