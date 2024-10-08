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
 * type ExpeditionSearchAllowedParam
 */
type ExpeditionSearchAllowedParam = ExpeditionSearchFilterType; // Add more values here if required. e.g. for page number and so on.

/**
 * type ExpeditionSearchFiltersFromUrl
 */
type ExpeditionSearchFiltersFromUrl = {
	destinations: string[],
	months: string[],
	itineraryLengths: [ number, number ],
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
	selectedFilters: ExpeditionSearchFilters;
	shipId: number,
	page: number,
	hasNextPage: boolean,
	resultCount: number,
	remainingCount: number,
	markup: string,
	noResultsMarkup: string,
	updateMarkup: boolean,
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
	itineraryLengths: [ number, number ],
	baseUrl: string,
	allowedParams: ExpeditionSearchAllowedParam[],
}

/**
 * Interface ExpeditionSearchStateUpdateObject
 */
interface ExpeditionsSearchStateUpdateObject extends Partial<ExpeditionSearchState>{}
