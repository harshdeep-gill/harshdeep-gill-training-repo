/**
 * Globals
 */
const { customElements } = window;

/**
 * Internal Dependencies
 */
import DatesRatesFilterChipElement from './chip';
import DatesRatesFiltersControllerElement from './controller';
import DatesRatesFilterCurrencyDropdownElement from './currency-dropdown';
import DatesRatesSelectedFiltersElement from './selected';
import DatesRatesSelectedFilterPillElement from './selected-pill';
import DatesRatesFilterStickyCurrencyElement from './sticky-currency';
import DatesRatesFilterCurrencyRadiosElement from './currency-radios';

/**
 * Define custom components.
 */
customElements.define( 'quark-dates-rates-filters-controller', DatesRatesFiltersControllerElement );
customElements.define( 'quark-dates-rates-filter-chip', DatesRatesFilterChipElement );
customElements.define( 'quark-dates-rates-selected-filters', DatesRatesSelectedFiltersElement );
customElements.define( 'quark-dates-rates-selected-filter-pill', DatesRatesSelectedFilterPillElement );
customElements.define( 'quark-dates-rates-filter-currency-dropdown', DatesRatesFilterCurrencyDropdownElement );
customElements.define( 'quark-dates-rates-filter-sticky-currency', DatesRatesFilterStickyCurrencyElement );
customElements.define( 'quark-dates-rates-filter-currency-radios', DatesRatesFilterCurrencyRadiosElement );
