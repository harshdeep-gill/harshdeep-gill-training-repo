/**
 * External dependencies.
 */
const { persist, create, stores } = window.zustand;

/**
 * Constants.
 */
export const STORE_NAME = 'searchFiltersBar';

/**
 * Default state.
 */
export const DEFAULT_STATE: SearchFiltersBarState = {
	filtersApiUrl: '',
	searchPageUrl: '',
	departureMonthOptions: [],
	destinationOptions: [],
	selectedDestinations: [],
	selectedMonths: new Set( [] ),
	resultCount: 0,
	initialized: false,
	history: [],
};

/**
 * Persistent keys.
 */
export const PERSISTENT_STATE_KEYS = [ 'history' ];

/**
 * Create store.
 */
const store = create(
	persist(
		() => DEFAULT_STATE,
		{
			name: STORE_NAME,
			partialize: ( state: SearchFiltersBarState ) => {
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
