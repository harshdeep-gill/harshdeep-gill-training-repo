/**
 * Interface DatesRatesFilterValue
 */
interface DatesRatesFilterState {
	value: string,
	label: string,
}

/**
 * type DatesRatesFilterType
 */
type DatesRatesFilterType = 'seasons' | 'expeditions' | 'adventureOptions' | 'months' | 'durations' | 'ships';

/**
 * type DatesRatesAllowedParam
 */
type DatesRatesAllowedParam = DatesRatesFilterType | 'perPage' | 'pageNumber';

/**
 * Interface DatesRatesState.
 */
interface DatesRatesState {
	selectedFilters: DatesRatesFilterType[],
	seasons: DatesRatesFilterState[],
	expeditions: DatesRatesFilterState[],
	adventureOptions: DatesRatesFilterState[],
	months: DatesRatesFilterState[],
	durations: DatesRatesFilterState[],
	ships: DatesRatesFilterState[],
	pageNumber: number,
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
	allowedParams: DatesRatesAllowedParam[],
	filtersMarkup: string,
	allowedPerPage: number[],
}

/**
 * Interface DatesRatesFiltersToUrl
 */
interface DatesRatesFiltersToUrl {
	selectedFilters: DatesRatesFilterType[],
	perPage: number,
	pageNumber: number,
}

/**
 * Interface DatesRatesFiltersFromUrl
 */
interface DatesRatesFiltersFromUrl {
	seasons: string[],
	expeditions: string[],
	adventureOptions: string[],
	months: string[],
	durations: string[],
	ships: string[],
	selectedFilters: DatesRatesFilterType[],
	perPage: number,
	pageNumber: number,
}

/**
 * Interface StateUpdateObject
 */
interface DatesRatesStateUpdateObject extends Partial<DatesRatesState> {}
