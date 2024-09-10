/**
 * Globals
 */
const { customElements } = window;

/**
 * Internal Dependencies
 */
import DatesRatesResultsElement from './results';
import DatesRatesResultsCountElement from './count';

// Define the element.
customElements.define( 'quark-dates-rates-results', DatesRatesResultsElement );
customElements.define( 'quark-dates-rates-results-count', DatesRatesResultsCountElement );
