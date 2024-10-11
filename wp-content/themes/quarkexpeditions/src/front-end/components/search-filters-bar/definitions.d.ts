/**
 * Interface SearchFiltersBarDestination
 */
interface SearchFiltersBarDestinationState {
	value: string,
	label: string,
	imageUrl: string,
	parent?: string,
}

/**
 * Interface SearchFiltersBarMonthsState
 */
interface SearchFiltersBarMonthState {
	value: string,
	label: string,
}

/**
 * Interface SearchFiltersBarHistoryState
 */
interface SearchFiltersBarHistoryState {
	destination: SearchFiltersBarDestinationState,
	month: SearchFiltersBarMonthState,
}

/**
 * Interface SearchFiltersBarState.
 */
interface SearchFiltersBarState {
	filtersApiUrl: string,
	searchPageUrl: string,
	selectedDestinations: SearchFiltersBarDestinationState[],
	selectedMonths: SearchFiltersBarMonthState[],
	departureMonthOptions: [],
	destinationOptions: [],
	history: SearchFiltersBarHistoryState[],
	resultCount: 0,
	initialized: false,
}
