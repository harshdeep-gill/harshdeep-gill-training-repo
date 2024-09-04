/**
 * Globals
 */
const { customElements } = window;

/**
 * Internal Dependencies
 */
import DatesRatesFilterChipElement from './chip';
import DatesRatesFilterCurrencyDropdownElement from './currency-dropdown';
import DatesRatesSelectedFiltersElement from './selected';
import DatesRatesSelectedFilterPillElement from './selected-pill';
import DatesRatesFilterStickyCurrencyElement from './sticky-currency';
import DatesRatesFilterCurrencyRadiosElement from './currency-radios';
import DatesRatesFilterStickyFilterElement from './sticky-filter';
import DatesRatesFilterSeasonsElement from './seasons';
import DatesRatesFilterExpeditionsElement from './expeditions';
import DatesRatesFilterAdventureOptionsElement from './adventure-options';
import DatesRatesFilterDepartureMonthsElement from './departure-months';
import DatesRatesFilterDurationsElement from './durations';
import DatesRatesFilterShipsElement from './ships';
import DatesRatesFiltersCtaViewResultsElement from './cta-view-results';
import DatesRatesFiltersCtaClearElement from './cta-clear';

/**
 * Define custom components.
 */
customElements.define( 'quark-dates-rates-filter-chip', DatesRatesFilterChipElement );
customElements.define( 'quark-dates-rates-selected-filters', DatesRatesSelectedFiltersElement );
customElements.define( 'quark-dates-rates-selected-filter-pill', DatesRatesSelectedFilterPillElement );
customElements.define( 'quark-dates-rates-filter-currency-dropdown', DatesRatesFilterCurrencyDropdownElement );
customElements.define( 'quark-dates-rates-filter-sticky-currency', DatesRatesFilterStickyCurrencyElement );
customElements.define( 'quark-dates-rates-filter-currency-radios', DatesRatesFilterCurrencyRadiosElement );
customElements.define( 'quark-dates-rates-filter-sticky-filter', DatesRatesFilterStickyFilterElement );
customElements.define( 'quark-dates-rates-filter-seasons', DatesRatesFilterSeasonsElement );
customElements.define( 'quark-dates-rates-filter-expeditions', DatesRatesFilterExpeditionsElement );
customElements.define( 'quark-dates-rates-filter-adventure-options', DatesRatesFilterAdventureOptionsElement );
customElements.define( 'quark-dates-rates-filter-departure-months', DatesRatesFilterDepartureMonthsElement );
customElements.define( 'quark-dates-rates-filter-durations', DatesRatesFilterDurationsElement );
customElements.define( 'quark-dates-rates-filter-ships', DatesRatesFilterShipsElement );
customElements.define( 'quark-dates-rates-filters-cta-view-results', DatesRatesFiltersCtaViewResultsElement );
customElements.define( 'quark-dates-rates-filters-cta-clear-all', DatesRatesFiltersCtaClearElement );
