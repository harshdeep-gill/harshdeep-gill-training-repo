/**
 * Globals
 */
const { customElements } = window;

/**
 * Internal Dependencies
 */
import DatesRatesFilterChip from './chip';
import DatesRatesFiltersControllerElement from './controller';
import DatesRatesSelectedFiltersElement from './selected';
import DatesRatesSelectedFilterPillElement from './selected-pill';

/**
 * Define custom components.
 */
customElements.define( 'quark-dates-rates-filters-controller', DatesRatesFiltersControllerElement );
customElements.define( 'quark-dates-rates-filter-chip', DatesRatesFilterChip );
customElements.define( 'quark-dates-rates-selected-filters', DatesRatesSelectedFiltersElement );
customElements.define( 'quark-dates-rates-selected-filter-pill', DatesRatesSelectedFilterPillElement );
