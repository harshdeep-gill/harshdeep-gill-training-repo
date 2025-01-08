/**
 * Global variables.
 */
const { customElements } = window;

/**
 * Internal dependencies.
 */
import ExpeditionSearchSidebarFiltersHeader from './header';
import ExpeditionSearchSidebarFilters from './sidebar-filters';
import ExpeditionSearchStickyFilters from './sticky-filters';
import ExpeditionSearchFilterDestinations from './content/expedition/destinations';
import ExpeditionSearchFilterMonths from './content/expedition/months';
import ExpeditionSearchFilterItineraryLengths from './content/expedition/itinerary-lengths';
import ExpeditionSearchFilterShips from './content/expedition/ships';
import ExpeditionSearchFilterAdventureOptions from './content/expedition/adventure-options';
import ExpeditionSearchFilterLanguages from './content/expedition/languages';
import ExpeditionSearchFilterExpeditions from './content/expedition/expeditions';
import ExpeditionSearchFilterCabinClasses from './content/cabin/cabin-classes';
import ExpeditionSearchFilterTravelers from './content/cabin/travelers';
import ExpeditionSearchFiltersCtaClearElement from './cta-clear';
import ExpeditionSearchFiltersCtaViewResultsElement from './cta-view-results';
import ExpeditionSearchSidebarFiltersInputContainerElement from './inputs-container';

// Define elements.
customElements.define( 'quark-expedition-search-sidebar-filters', ExpeditionSearchSidebarFilters );
customElements.define( 'quark-expedition-search-sticky-filters', ExpeditionSearchStickyFilters );
customElements.define( 'quark-expedition-search-sidebar-filters-header', ExpeditionSearchSidebarFiltersHeader );
customElements.define( 'quark-expedition-search-filter-destinations', ExpeditionSearchFilterDestinations );
customElements.define( 'quark-expedition-search-filter-months', ExpeditionSearchFilterMonths );
customElements.define( 'quark-expedition-search-filter-itinerary-lengths', ExpeditionSearchFilterItineraryLengths );
customElements.define( 'quark-expedition-search-filter-ships', ExpeditionSearchFilterShips );
customElements.define( 'quark-expedition-search-filter-adventure-options', ExpeditionSearchFilterAdventureOptions );
customElements.define( 'quark-expedition-search-filter-languages', ExpeditionSearchFilterLanguages );
customElements.define( 'quark-expedition-search-filter-expeditions', ExpeditionSearchFilterExpeditions );
customElements.define( 'quark-expedition-search-filter-cabin-classes', ExpeditionSearchFilterCabinClasses );
customElements.define( 'quark-expedition-search-filter-travelers', ExpeditionSearchFilterTravelers );
customElements.define( 'quark-expedition-search-filters-cta-clear-all', ExpeditionSearchFiltersCtaClearElement );
customElements.define( 'quark-expedition-search-filters-cta-view-results', ExpeditionSearchFiltersCtaViewResultsElement );
customElements.define( 'quark-expedition-search-sidebar-filters-inputs-container', ExpeditionSearchSidebarFiltersInputContainerElement );
