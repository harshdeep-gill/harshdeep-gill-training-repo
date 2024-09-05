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
	areCurrencyFiltersSyncing: boolean,
	seasons: DatesRatesFilterState[],
	expeditions: DatesRatesFilterState[],
	'adventure_options': DatesRatesFilterState[],
	months: DatesRatesFilterState[],
	durations: DatesRatesFilterState[],
	ships: DatesRatesFilterState[],
	page: number,
	totalPages: number,
	perPage: number,
}
