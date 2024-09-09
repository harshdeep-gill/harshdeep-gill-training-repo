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
	seasons: [],
	expeditions: [],
	adventure_options: [],
	months: [],
	durations: [],
	ships: [],
	page: 1,
	totalPages: 1,
	perPage: 10,
	totalItems: 10,
	isLoading: false,
	expeditionId: 0,
	partial: '',
	selector: '',
	isInitialized: false,
	shouldMarkupUpdate: false,
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
