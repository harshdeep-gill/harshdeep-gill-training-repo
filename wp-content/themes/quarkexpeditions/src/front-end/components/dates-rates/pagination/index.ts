/**
 * Globals
 */
const { customElements } = window;

/**
 * Internal Dependencies
 */
import DatesRatesPaginationPageNumberElement from './page-number';
import DatesRatesPaginationPrevPageElement from './prev';
import DatesRatesPaginationNextPageElement from './next';
import DatesRatesPaginationLinksControllerElement from './links';
import DatesRatesPaginationTotalPagesElement from './total-pages';
import DatesRatesPaginationItemsPerPageElement from './items-per-page';

// Define elements
customElements.define( 'quark-dates-rates-pagination-page-number', DatesRatesPaginationPageNumberElement );
customElements.define( 'quark-dates-rates-pagination-prev-page', DatesRatesPaginationPrevPageElement );
customElements.define( 'quark-dates-rates-pagination-next-page', DatesRatesPaginationNextPageElement );
customElements.define( 'quark-dates-rates-pagination-links-controller', DatesRatesPaginationLinksControllerElement );
customElements.define( 'quark-dates-rates-pagination-total-pages', DatesRatesPaginationTotalPagesElement );
customElements.define( 'quark-dates-rates-pagination-items-per-page', DatesRatesPaginationItemsPerPageElement );
