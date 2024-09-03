/**
 * Interface DatesRatesFilters.
 */
interface DatesRatesFilters {
	'currency'?: string,
	'seasons'?: string[],
	'months'?: string[],
	'expeditions'?: string[],
	'adventure_options'?: string[],
	'durations'?: string[],
	'ships'?: string[],
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
