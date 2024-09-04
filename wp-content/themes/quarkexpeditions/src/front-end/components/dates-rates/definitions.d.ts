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
	selectedFilters: DatesRatesFilters,
	areCurrencyFiltersSyncing: boolean,
	seasons: DatesRatesFilterState[],
	expeditions: DatesRatesFilterState[],
	'adventure_options': DatesRatesFilterState[],
	months: DatesRatesFilterState[],
	durations: DatesRatesFilterState[],
	ships: DatesRatesFilterState[],
}
