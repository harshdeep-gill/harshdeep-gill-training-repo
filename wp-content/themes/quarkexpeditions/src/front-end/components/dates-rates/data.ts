/**
 * External dependencies.
 */
const { persist, create, stores } = window.zustand;

/**
 * Constants.
 */
export const STORE_NAME = 'datesRates';

/**
 * Default state.
 */
export const DEFAULT_STATE: DatesRatesState = {
	currency: 'USD',
	page: 1,
	totalPages: 1,
	perPage: 8,
	resultCount: 0,
	isLoading: false,
	partial: '',
	selector: '',
	isInitialized: false,
	shouldMarkupUpdate: false,
	markup: '',
	noResultsMarkup: '',
	baseUrl: '',
	allowedParams: [
		'seasons',
		'expeditions',
		'adventureOptions',
		'months',
		'durations',
		'ships',
		'perPage',
	],
	filtersMarkup: '',
	selectedFilters: [],
};

/**
 * Persistent keys.
 */
export const PERSISTENT_STATE_KEYS = [];

/**
 * Create store.
 */
const store = create(
	persist(
		() => DEFAULT_STATE,
		{
			name: STORE_NAME,
			partialize: ( state: DatesRatesState ) => {
				// Create persistent state variable.
				const persistentState = {};
				PERSISTENT_STATE_KEYS.forEach( ( key: string ) => {
					// Set persistent state.
					persistentState[ key as keyof object ] = state[ key as keyof object ];
				} );

				// Return persistent state.
				return persistentState;
			},
		},
	),
);

// Set store value.
stores[ STORE_NAME ] = store;

// Export store.
export default store;
