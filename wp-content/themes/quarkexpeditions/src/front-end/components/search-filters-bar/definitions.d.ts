/**
 * Interface BookDeparturesShipsFilters.
 */
interface SearchFiltersBarFilters {
	'destinations'?: Set<string>,
	'months'?: Set<string>,
	[key: string]: any;
}

/**
 * Interface SearchFiltersBarState.
 */
interface SearchFiltersBarState {
	filtersApiUrl: string,
	selectedFilters: SearchFiltersBarFilters;
	departureMonthOptions: [],
	destinationOptions: [],
	resultCount: 0,
	refreshDestinations: false,
	refreshDepartures: false,
	initialized: false,
}
