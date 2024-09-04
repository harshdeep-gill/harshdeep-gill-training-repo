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
 * Interface DatesRatesFilter
 */
interface DatesRatesFilter {
	value: string,
	label: string,
}

/**
 * Interface DatesRatesState.
 */
interface DatesRatesState {
	selectedFilters: DatesRatesFilters,
	areCurrencyFiltersSyncing: boolean,
	seasons: DatesRatesFilter[],
}
