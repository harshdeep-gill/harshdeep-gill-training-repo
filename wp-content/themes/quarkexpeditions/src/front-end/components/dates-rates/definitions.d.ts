/**
 * Interface DatesRatesFilters.
 */
interface DatesRatesFilters {
	'currency'?: string,
	'sort'?: string,
	'seasons'?: string,
	'months'?: string,
	'expeditions'?: number,
	'adventure_options'?: number,
	'durations'?: number,
	'ships'?: number
	[key: string]: any;
}

/**
 * Interface DatesRatesState.
 */
interface DatesRatesState {
	partial: string,
	selector: string,
	selectedFilters: DatesRatesFilters;
	expeditionId: 0,
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
