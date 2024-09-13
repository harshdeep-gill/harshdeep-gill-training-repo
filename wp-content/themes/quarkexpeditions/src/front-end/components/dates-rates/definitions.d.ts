/**
 * Interface DatesRatesFilterValue
 */
interface DatesRatesFilterValue {
	value: string,
	label: string,
}

/**
 * Interface DatesRatesFilterState
 */
interface DatesRatesFilterState {
	name: string,
	values: DatesRatesFilterValue[],
}

/**
 * Interface DatesRatesState.
 */
interface DatesRatesState {
	currency: string,
	seasons: DatesRatesFilterValue[],
	expeditions: DatesRatesFilterValue[],
	adventureOptions: DatesRatesFilterValue[],
	months: DatesRatesFilterValue[],
	durations: DatesRatesFilterValue[],
	ships: DatesRatesFilterValue[],
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
	selectedFilters: DatesRatesFilterState[],
}

/**
 * Interface DatesRatesFiltersInUrl
 */
interface DatesRatesFiltersInUrl {
	seasons: string[],
	expeditions: string[],
	adventureOptions: string[],
	months: string[],
	durations: string[],
	ships: string[],
	perPage: number,
}

/**
 * Interface StateUpdateObject
 */
interface DatesRatesStateUpdateObject extends Partial<DatesRatesState> {}
