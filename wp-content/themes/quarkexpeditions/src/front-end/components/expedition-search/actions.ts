/**
 * External dependencies.
 */
const { zustand, fetchPartial } = window;

/**
 * Internal dependencies.
 */
const { setState, getState } = zustand.stores.expeditionSearch;

/**
 * Initialize data for the component.
 *
 * @param {Object} settings          Settings.
 * @param {string} settings.partial  Partial Url.
 * @param {string} settings.selector Selector.
 * @param {string} settings.shipId   Ship ID.
 */
export const initialize = ( settings: {
	partial: string | undefined,
	selector: string | undefined,
	shipId: number | undefined,
} ): void => {
	// Get current state.
	const currentState: ExpeditionSearchState = getState();
	const selectedFilters: ExpeditionSearchFilters = {
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
	const { selectedFilters, loadMoreResults, page, partial, selector, shipId } = getState();

	// Set loading: true if not loading more results.
	if ( ! loadMoreResults ) {
		setState( {
			loading: true,
		} );
	}

	// Fetch partial.
	fetchPartial( partial, {
		selectedFilters: {
			...selectedFilters,
			page,
			ships: [ shipId ],
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
export const loadMoreResults = () => {
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

/**
 * Turn off the show filters state.
 */
export const hideSearchFiltersAction = () => {
	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = { areSearchFiltersShown: false };

	// Hide the filters.
	setState( updateObject );
};

/**
 * Turn on the show filters state.
 */
export const showSearchFiltersAction = () => {
	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = { areSearchFiltersShown: true };

	// Show the filters.
	setState( updateObject );
};

/**
 * Adds a list of destinations to the state.
 *
 * @param { Object[] } destinationsToAdd The list of destinations to add to the state.
 */
export const addDestinations = ( destinationsToAdd: ExpeditionSearchFilterState[] ) => {
	// Get the state.
	const { destinations }: ExpeditionSearchState = getState();

	// Initialize the update Object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {
		destinations: [ ...destinations ],
	};

	// Loop through the valid destinations.
	destinationsToAdd.forEach( ( destinationToAdd ) => {
		// Sanity check.
		if (
			'' === destinationToAdd.value ||
			'' === destinationToAdd.label ||
			( updateObject.destinations && updateObject.destinations?.findIndex( ( existingDestination ) => existingDestination.value === destinationToAdd.value ) > -1 )
		) {
			// Bail.
			return;
		}

		// Remove the children of the current valid destination if any.
		updateObject.destinations = updateObject.destinations?.filter( ( existingDestination ) => existingDestination.parent !== destinationToAdd.value );

		// Push the valid Destination.
		updateObject.destinations?.push( destinationToAdd );
	} );

	// Set the state
	setState( updateObject );
};

/**
 * Adds a destination.
 *
 * @param { Object } destinationToAdd the destination object.
 */
export const addDestination = ( destinationToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === destinationToAdd.value || '' === destinationToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { destinations }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( destinations.findIndex( ( existingDestination ) => existingDestination.value === destinationToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Does this destination have a parent?
	if ( ! destinationToAdd.parent ) {
		// Nope, it is the parent destination. Add it after removing its children.
		updateObject.destinations = destinations.filter( ( existingDestination ) => existingDestination.parent !== destinationToAdd.value );
		updateObject.destinations.push( destinationToAdd );
	} else {
		// Just add it normally.
		updateObject.destinations = [ ...destinations, destinationToAdd ];
	}

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a destination.
 *
 * @param { string } destinationValue The value of the destination to remove.
 */
export const removeDestination = ( destinationValue: string ) => {
	// Get the state.
	const { destinations }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the destinations.
	updateObject.destinations = destinations.filter( ( existingDestination ) => existingDestination.value !== destinationValue && existingDestination.parent !== destinationValue );

	// Set the state.
	setState( updateObject );
};

/**
 * Adds a ship.
 *
 * @param { Object } shipToAdd the ship object.
 */
export const addShip = ( shipToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === shipToAdd.value || '' === shipToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { ships }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( ships.findIndex( ( existingShip ) => existingShip.value === shipToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the ship.
	updateObject.ships = [ ...ships, shipToAdd ];

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a ship.
 *
 * @param { string } shipValue The value of the ship to remove.
 */
export const removeShip = ( shipValue: string ) => {
	// Get the state.
	const { ships }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the ships.
	updateObject.ships = ships.filter( ( existingShip ) => existingShip.value !== shipValue );

	// Set the state.
	setState( updateObject );
};

/**
 * Adds a adventureOption.
 *
 * @param { Object } adventureOptionToAdd the adventureOption object.
 */
export const addAdventureOption = ( adventureOptionToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === adventureOptionToAdd.value || '' === adventureOptionToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { adventureOptions }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( adventureOptions.findIndex( ( existingAdventureOption ) => existingAdventureOption.value === adventureOptionToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the adventureOption.
	updateObject.adventureOptions = [ ...adventureOptions, adventureOptionToAdd ];

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a adventureOption.
 *
 * @param { string } adventureOptionValue The value of the adventureOption to remove.
 */
export const removeAdventureOption = ( adventureOptionValue: string ) => {
	// Get the state.
	const { adventureOptions }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the adventureOptions.
	updateObject.adventureOptions = adventureOptions.filter( ( existingAdventureOption ) => existingAdventureOption.value !== adventureOptionValue );

	// Set the state.
	setState( updateObject );
};

/**
 * Adds a language.
 *
 * @param { Object } languageToAdd the language object.
 */
export const addLanguage = ( languageToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === languageToAdd.value || '' === languageToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { languages }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( languages.findIndex( ( existingLanguage ) => existingLanguage.value === languageToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the language.
	updateObject.languages = [ ...languages, languageToAdd ];

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a language.
 *
 * @param { string } languageValue The value of the language to remove.
 */
export const removeLanguage = ( languageValue: string ) => {
	// Get the state.
	const { languages }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the languages.
	updateObject.languages = languages.filter( ( existingLanguage ) => existingLanguage.value !== languageValue );

	// Set the state.
	setState( updateObject );
};

/**
 * Adds a expedition.
 *
 * @param { Object } expeditionToAdd the expedition object.
 */
export const addExpedition = ( expeditionToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === expeditionToAdd.value || '' === expeditionToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { expeditions }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( expeditions.findIndex( ( existingExpedition ) => existingExpedition.value === expeditionToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the expedition.
	updateObject.expeditions = [ ...expeditions, expeditionToAdd ];

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a expedition.
 *
 * @param { string } expeditionValue The value of the expedition to remove.
 */
export const removeExpedition = ( expeditionValue: string ) => {
	// Get the state.
	const { expeditions }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the expeditions.
	updateObject.expeditions = expeditions.filter( ( existingExpedition ) => existingExpedition.value !== expeditionValue );

	// Set the state.
	setState( updateObject );
};

/**
 * Adds a cabinClass.
 *
 * @param { Object } cabinClassToAdd the cabinClass object.
 */
export const addCabinClass = ( cabinClassToAdd: ExpeditionSearchFilterState ) => {
	// Sanity check.
	if ( '' === cabinClassToAdd.value || '' === cabinClassToAdd.label ) {
		// Bail.
		return;
	}

	// Get the state.
	const { cabinClasses }: ExpeditionSearchState = getState();

	// Check if it is already selected.
	if ( cabinClasses.findIndex( ( existingCabinClass ) => existingCabinClass.value === cabinClassToAdd.value ) > -1 ) {
		// Yes it is. Bail.
		return;
	}

	// Initialize update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Add the cabinClass.
	updateObject.cabinClasses = [ ...cabinClasses, cabinClassToAdd ];

	// Set the state;
	setState( updateObject );
};

/**
 * Removes a cabinClass.
 *
 * @param { string } cabinClassValue The value of the cabinClass to remove.
 */
export const removeCabinClass = ( cabinClassValue: string ) => {
	// Get the state.
	const { cabinClasses }: ExpeditionSearchState = getState();

	// Initialize the update object.
	const updateObject: ExpeditionsSearchStateUpdateObject = {};

	// Filter the cabinClasses.
	updateObject.cabinClasses = cabinClasses.filter( ( existingCabinClass ) => existingCabinClass.value !== cabinClassValue );

	// Set the state.
	setState( updateObject );
};
