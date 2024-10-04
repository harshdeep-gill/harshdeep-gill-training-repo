/**
 * Interface ExpeditionSearchFilters.
 */
interface ExpeditionSearchFilters {
	'sort'?: string,
	[key: string]: any;
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
}
