/**
 * External dependencies.
 */
const { persist, create, stores } = window.zustand;

/**
 * Constants.
 */
export const STORE_NAME = 'expeditionSearch';

/**
 * Default state.
 */
export const DEFAULT_STATE: ExpeditionSearchState = {
	partial: '',
	selector: '',
	selectedFilters: [],
	shipId: 0,
	page: 1,
	nextPage: 1,
	hasNextPage: false,
	resultCount: 0,
	remainingCount: 0,
	markup: '',
	filtersMarkup: '',
	compactFiltersMarkup: '',
	noResultsMarkup: '',
	updateMarkup: false,
	updateFiltersMarkup: false,
	updateCompactFiltersMarkup: false,
	resetMarkup: false,
	loadMoreResults: false,
	loading: false,
	initialized: false,
	areSearchFiltersShown: true,
	destinations: [],
	ships: [],
	adventureOptions: [],
	languages: [],
	expeditions: [],
	cabinClasses: [],
	travelers: [],
	months: [],
	itineraryLengths: [],
	initialItineraryLengths: [],
	baseUrl: '',
	allowedParams: [
		'destinations',
		'months',
		'itineraryLengths',
		'ships',
		'adventureOptions',
		'languages',
		'expeditions',
		'cabinClasses',
		'travelers',
	],
	sort: '',
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
			partialize: ( state: ExpeditionSearchState ) => {
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
