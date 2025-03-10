<quark-expedition-search-sidebar-filters-header class="expedition-search__sidebar-filters-header">
	<x-button
		size="big"
		appearance="outline"
		color="white"
		class="expedition-search__sidebar-filters-show-button"
		icon="filters"
		icon_position="left"
		data-hidden=""
	>
		{{ __( 'Show Filters', 'qrk' ) }} <span class="expedition-search__filter-count"></span>
	</x-button>
	<h2 class="h4 expedition-search__sidebar-filters-header-title">
		<x-escape :content="__( 'Filters', 'qrk' )" />
		<span class="expedition-search__filter-count"></span>
	</h2>
	<button class="expedition-search__sidebar-filters-hide-button" type="button">{{ __( 'Hide Filters', 'qrk' ) }}</button>
</quark-expedition-search-sidebar-filters-header>
