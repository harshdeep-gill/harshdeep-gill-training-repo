/**
 * Interface ExpeditionSearchFilters.
 */
interface ExpeditionSearchFilters {
	'sort'?: string,
	[key: string]: any;
}

/**
 * Interface ExpeditionSearchFilterState
 */
interface ExpeditionSearchFilterState {
	value: string,
	label: string,
	parent?: string,
}

/**
 * type ExpeditionSearchFilterType
 */
type ExpeditionSearchFilterType = 'destinations' |
	'months' |
	'itineraryLengths' |
	'adventureOptions' |
	'languages' |
	'ships' |
	'expeditions' |
	'cabinClasses' |
	'travelers';

/**
 * type ExpeditionSearchAllowedPara
 */
type ExpeditionSearchAllowedParam = ExpeditionSearchFilterType; // Add more values here if required. e.g. for page number and so on.

/**
 * type ExpeditionSearchFiltersFromUrl
 */
type ExpeditionSearchFiltersFromUrl = {
	selectedFilters: ExpeditionSearchFilterType[],
	destinations: string[],
	months: string[],
	itineraryLengths: number[],
	adventureOptions: string[],
	languages: string[],
	ships: string[],
	expeditions: string[],
	cabinClasses: string[],
	travelers: string[],
}

/**
 * Interface ExpeditionSearchState.
 */
interface ExpeditionSearchState {
	partial: string,
	selector: string,
	selectedFilters: ExpeditionSearchFilterType[];
	shipId: number,
	page: number,
	nextPage: number,
	hasNextPage: boolean,
	resultCount: number,
	remainingCount: number,
	markup: string,
	noResultsMarkup: string,
	updateMarkup: boolean,
	updateFiltersMarkup: boolean,
	updateCompactFiltersMarkup: boolean,
	resetMarkup: boolean,
	initialized: boolean,
	loading: boolean,
	loadMoreResults: boolean,
	areSearchFiltersShown: boolean,
	destinations: ExpeditionSearchFilterState[],
	ships: ExpeditionSearchFilterState[],
	adventureOptions: ExpeditionSearchFilterState[],
	languages: ExpeditionSearchFilterState[],
	expeditions: ExpeditionSearchFilterState[],
	cabinClasses: ExpeditionSearchFilterState[],
	travelers: ExpeditionSearchFilterState[],
	months: ExpeditionSearchFilterState[],
	itineraryLengths: number[],
	initialItineraryLengths: number[],
	baseUrl: string,
	allowedParams: ExpeditionSearchAllowedParam[],
	filtersMarkup: string,
	compactFiltersMarkup: string,
	sort: string,
}

/**
 * Interface ExpeditionSearchStateUpdateObject
 */
interface ExpeditionsSearchStateUpdateObject extends Partial<ExpeditionSearchState>{}
