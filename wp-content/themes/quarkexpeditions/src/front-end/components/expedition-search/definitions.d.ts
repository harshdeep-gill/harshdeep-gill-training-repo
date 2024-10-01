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
 * Interface ExpeditionSearchState.
 */
interface ExpeditionSearchState {
	partial: string,
	selector: string,
	selectedFilters: ExpeditionSearchFilters;
	shipId: 0,
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
}

/**
 * Interface ExpeditionSearchStateUpdateObject
 */
interface ExpeditionsSearchStateUpdateObject extends Partial<ExpeditionSearchState>{}
