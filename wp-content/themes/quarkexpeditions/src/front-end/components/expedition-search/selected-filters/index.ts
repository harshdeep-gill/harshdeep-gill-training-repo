/**
 * Global variables.
 */
const { customElements } = window;

/**
 * Internal dependencies.
 */
import ExpeditionSearchSelectedFilters from './selected-filters';
import ExpeditionSearchSelectedFilterPill from './selected-filter-pill';

// Define elements.
customElements.define( 'quark-expedition-search-selected-filters', ExpeditionSearchSelectedFilters );
customElements.define( 'quark-expedition-search-selected-filter-pill', ExpeditionSearchSelectedFilterPill );
