/**
 * External dependencies.
 */
const { persist, create, stores } = window.zustand;

/**
 * Constants.
 */
export const STORE_NAME = 'bookDeparturesShips';

/**
 * Default state.
 */
export const DEFAULT_STATE: BookDeparturesShipsState = {
	partial: '',
	selector: '',
	selectedFilters: {},
	shipId: 0,
	page: 1,
	hasNextPage: false,
	resultCount: 0,
	remainingCount: 0,
	markup: '',
	noResultsMarkup: '',
	updateMarkup: false,
	resetMarkup: false,
	loadMoreResults: false,
	loading: false,
	initialized: false,
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
			partialize: ( state: BookDeparturesShipsState ) => {
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
