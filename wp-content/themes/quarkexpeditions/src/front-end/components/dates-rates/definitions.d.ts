/**
 * Interface DatesRatesFilterState
 */
interface DatesRatesFilterState {
	value: string,
	label: string,
}

/**
 * Interface DatesRatesState.
 */
interface DatesRatesState {
	currency: string,
	seasons: DatesRatesFilterState[],
	expeditions: DatesRatesFilterState[],
	adventureOptions: DatesRatesFilterState[],
	months: DatesRatesFilterState[],
	durations: DatesRatesFilterState[],
	ships: DatesRatesFilterState[],
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
}

/**
 * Interface DatesRatesFiltersSaved
 */
interface DatesRatesFiltersSaved {
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
