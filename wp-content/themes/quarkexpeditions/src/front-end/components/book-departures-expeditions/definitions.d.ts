/**
 * Interface BookDeparturesExpeditionsFilters.
 */
interface BookDeparturesExpeditionsFilters {
	'currency'?: string,
	'sort'?: string,
	[key: string]: any;
}

/**
 * Interface BookDeparturesExpeditionsState.
 */
interface BookDeparturesExpeditionsState {
	partial: string,
	selector: string,
	selectedFilters: BookDeparturesExpeditionsFilters;
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
