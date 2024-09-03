/**
 * Global variables.
 */
const { customElements } = window;

/**
 * Internal Dependencies
 */
import './data';
import DatesRatesFilterChip from './filter';
import DatesRatesFiltersControllerElement from './filters';
import DatesRatesResultsElement from './results';

/**
 * Define custom components.
 */
customElements.define( 'quark-dates-rates-filter-chip', DatesRatesFilterChip );
customElements.define( 'quark-dates-rates-filters-controller', DatesRatesFiltersControllerElement );
customElements.define( 'quark-dates-rates-results', DatesRatesResultsElement );
