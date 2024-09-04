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
		selectedFilters: updatedFilters,
		areCurrencyFiltersSyncing: true,
	} );

	// Disable syncing status.
	setState( {
		areCurrencyFiltersSyncing: false,
	} );

	//TODO: fetch results
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
		selectedFilters: updatedFilters,
	} );

	//TODO: fetch results
};
