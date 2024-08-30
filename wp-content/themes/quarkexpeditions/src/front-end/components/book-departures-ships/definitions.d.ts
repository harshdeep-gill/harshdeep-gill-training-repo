/**
 * Interface BookDeparturesShipsFilters.
 */
interface BookDeparturesShipsFilters {
	'currency'?: string,
	'sort'?: string,
	[key: string]: any;
}

/**
 * Interface BookDeparturesShipsState.
 */
interface BookDeparturesShipsState {
	partial: string,
	selector: string,
	selectedFilters: BookDeparturesShipsFilters;
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
