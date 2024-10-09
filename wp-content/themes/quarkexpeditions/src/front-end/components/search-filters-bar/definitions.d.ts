/**
 * Interface SearchFiltersBarDestination
 */
interface SearchFiltersBarDestinationState {
	value: string,
	label: string,
	imageUrl: string,
}

/**
 * Interface SearchFiltersBarMonthsState
 */
interface SearchFiltersBarMonthState {
	value: string,
	label: string,
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
	history: [],
	resultCount: 0,
	initialized: false,
}
