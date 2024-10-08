/**
 * Interface SearchFiltersBarState.
 */
interface SearchFiltersBarState {
	filtersApiUrl: string,
	searchPageUrl: string,
	selectedDestinations: Set<string>,
	selectedMonths: Set<string>,
	departureMonthOptions: [],
	destinationOptions: [],
	resultCount: 0,
	initialized: false,
}
