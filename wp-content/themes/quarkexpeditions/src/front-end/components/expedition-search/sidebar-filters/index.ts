/**
 * Global variables.
 */
const { customElements } = window;

/**
 * Internal dependencies.
 */
import ExpeditionSearchSidebarFiltersHeader from './header';
import ExpeditionSearchSidebarFilters from './sidebar-filters';
import ExpeditionSearchFilterDestinations from './content/expedition/destinations';
import ExpeditionSearchFilterShips from './content/expedition/ships';

// Define elements.
customElements.define( 'quark-expedition-search-sidebar-filters', ExpeditionSearchSidebarFilters );
customElements.define( 'quark-expedition-search-sidebar-filters-header', ExpeditionSearchSidebarFiltersHeader );
customElements.define( 'quark-expedition-search-filter-destinations', ExpeditionSearchFilterDestinations );
customElements.define( 'quark-expedition-search-filter-ships', ExpeditionSearchFilterShips );
