/**
 * Interface DatesRatesFilterValue
 */
interface DatesRatesSelectedFilter {
	value: string,
	label: string,
}

/**
 * type DatesRatesFilterType
 */
type DatesRatesFilterType = 'seasons' | 'expeditions' | 'adventureOptions' | 'months' | 'durations' | 'ships';

/**
 * Interface DatesRatesFilterState
 */
interface DatesRatesFilterState {
	type: DatesRatesFilterType,
	filters: DatesRatesSelectedFilter[],
}

/**
 * Interface DatesRatesFilterStateUpdateObject
 */
interface DatesRatesFilterStateUpdateObject {
	type: DatesRatesFilterType,
	filter: DatesRatesSelectedFilter,
}

/**
 * Interface DatesRatesState.
 */
interface DatesRatesState {
	selectedFilters: DatesRatesFilterState[],
	page: number,
	totalPages: number,
	perPage: number,
	resultCount: number,
	isLoading: boolean,
	partial: string,
	selector: string,
	isInitialized: boolean,
	shouldMarkupUpdate: boolean,
	markup: string,
	noResultsMarkup: string,
	baseUrl: string
	allowedParams: string[],
	filtersMarkup: string,
}

/**
 * Interface DatesRatesFiltersInUrl
 */
interface DatesRatesFiltersInUrl {
	selectedFilters: DatesRatesFilterState[],
	perPage: number,
}

/**
 * Interface StateUpdateObject
 */
interface DatesRatesStateUpdateObject extends Partial<DatesRatesState> {}
