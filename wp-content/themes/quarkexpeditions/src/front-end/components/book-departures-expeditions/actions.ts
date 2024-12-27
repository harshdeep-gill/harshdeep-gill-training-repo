/**
 * External dependencies.
 */
const { zustand, fetchPartial } = window;

/**
 * Internal dependencies.
 */
const { setState, getState } = zustand.stores.bookDeparturesExpeditions;

/**
 * External dependencies.
 */
const { yieldToMain } = window;

/**
 * Initialize data for the component.
 *
 * @param {Object} settings              Settings.
 * @param {string} settings.partial      Partial Url.
 * @param {string} settings.selector     Selector.
 * @param {string} settings.expeditionId Expedition ID.
 */
export const initialize = ( settings: {
	partial: string | undefined,
	selector: string | undefined,
	expeditionId: number | undefined,
} ): void => {
	// Get current state.
	const currentState: BookDeparturesExpeditionsState = getState();
	const selectedFilters: BookDeparturesExpeditionsFilters = {
		sort: 'date-now',
	};

	// Initialize: Add settings in state.
	setState( {
		...currentState,
		...settings,
		selectedFilters,
		initialized: true,
		updateMarkup: true,
	} );

	// Fetch Results.
	fetchResults( stateInitialized );
};

/**
 * State Initialized call back.
 *
 * @param {Object} response Response.
 */
const stateInitialized = ( response: PartialData ) => {
	// Get state.
	const { page } = getState();

	// Get response data.
	const {
		markup,
		noResultsMarkup,
		data: { resultCount, nextPage, remainingCount },
	} = response;

	// Set State.
	setState( {
		loading: false,
		markup,
		noResultsMarkup,
		initialized: true,
		resultCount,
		remainingCount,
		hasNextPage: nextPage && nextPage > page,
		nextPage,
	} );
};

/**
 * Update sort filter value.
 *
 * @param {string} sort Selected Sort.
 */
export const updateSort = ( sort: string ) => {
	// Get State.
	const { selectedFilters } = getState();
	const updatedFilters = { ...selectedFilters };

	// If sort exists, update the value.
	if ( sort ) {
		updatedFilters.sort = sort;
	}

	// Set state.
	setState( {
		loading: true,
		selectedFilters: updatedFilters,
		page: 1,
		updateMarkup: true,
	} );

	// Fetch Results.
	fetchResults( filterUpdated );
};

/**
 * Filters updated callback.
 *
 * @param {Object} response Response.
 */
const filterUpdated = ( response: PartialData ) => {
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

/**
 * Fetch Results.
 *
 * @param {Function} callback Callback function.
 */
export const fetchResults = ( callback: Function ) => {
	// Get data from state.
	const { selectedFilters, page, partial, selector, expeditionId } = getState();

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
 * Markup updated callback.
 */
export const markupUpdated = () => {
	// Reset all the relevant states, when markup is updated.
	setState( {
		loading: false,
		updateMarkup: false,
		loadMoreResults: false,
		resetMarkup: false,
		markup: '',
	} );
};

/**
 * Load more results.
 */
export const loadMoreResults = async () => {
	// Update UI by yielding to main thread.
	await yieldToMain();

	// Get state.
	const { hasNextPage, nextPage } = getState();

	// If page no is not available, early return.
	if ( ! hasNextPage || ! nextPage ) {
		// Return.
		return;
	}

	// Set state to load more posts with new page no.
	setState( {
		page: nextPage,
		loadMoreResults: true,
	} );

	// Fetch results.
	fetchResults( moreResultsLoaded );
};

/**
 * More results loaded callback.
 *
 * @param {Object} response Response.
 */
const moreResultsLoaded = ( response: PartialData ) => {
	// Get state.
	const { page } = getState();
	const { markup, data: { nextPage, remainingCount } } = response;

	// Set State.
	setState( {
		markup,
		hasNextPage: nextPage && nextPage > page,
		nextPage,
		updateMarkup: true,
		remainingCount,
		page,
	} );
};

/**
 * Set loading state.
 *
 * @param {boolean} loading Loading state.
 */
export const setLoading = ( loading: boolean ) => {
	// Set loading state.
	setState( { loading } );
};
