/**
 * Interface SearchFiltersBarDestination
 */
interface SearchFiltersBarDestinationState {
	value: string,
	label: string,
	imageUrl: string,
}

/**
 * Interface SearchFiltersBarState.
 */
interface SearchFiltersBarState {
	filtersApiUrl: string,
	searchPageUrl: string,
	selectedDestinations: SearchFiltersBarDestinationState[],
	selectedMonths: Set<string>,
	departureMonthOptions: [],
	destinationOptions: [],
	history: [],
	resultCount: 0,
	initialized: false,
}
