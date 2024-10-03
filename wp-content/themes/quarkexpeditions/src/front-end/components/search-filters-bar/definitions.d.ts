/**
 * Interface SearchFiltersBarState.
 */
interface SearchFiltersBarState {
	filtersApiUrl: string,
	selectedDestinations: Set<string>,
	selectedMonths: Set<string>,
	departureMonthOptions: [],
	destinationOptions: [],
	resultCount: 0,
	refreshDestinations: false,
	refreshDepartures: false,
	initialized: false,
}
