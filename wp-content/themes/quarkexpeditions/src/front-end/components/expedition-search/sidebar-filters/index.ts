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
import ExpeditionSearchFilterAdventureOptions from './content/expedition/adventure-options';
import ExpeditionSearchFilterLanguages from './content/expedition/languages';
import ExpeditionSearchFilterExpeditions from './content/expedition/expeditions';
import ExpeditionSearchFilterCabinClasses from './content/cabin/cabin-classes';

// Define elements.
customElements.define( 'quark-expedition-search-sidebar-filters', ExpeditionSearchSidebarFilters );
customElements.define( 'quark-expedition-search-sidebar-filters-header', ExpeditionSearchSidebarFiltersHeader );
customElements.define( 'quark-expedition-search-filter-destinations', ExpeditionSearchFilterDestinations );
customElements.define( 'quark-expedition-search-filter-ships', ExpeditionSearchFilterShips );
customElements.define( 'quark-expedition-search-filter-adventure-options', ExpeditionSearchFilterAdventureOptions );
customElements.define( 'quark-expedition-search-filter-languages', ExpeditionSearchFilterLanguages );
customElements.define( 'quark-expedition-search-filter-expeditions', ExpeditionSearchFilterExpeditions );
customElements.define( 'quark-expedition-search-filter-cabin-classes', ExpeditionSearchFilterCabinClasses );
