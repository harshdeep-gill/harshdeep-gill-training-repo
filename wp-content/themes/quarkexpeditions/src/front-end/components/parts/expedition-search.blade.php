@props( [
	'results_count'   => 0,
	'remaining_count' => 0,
	'cards'           => Quark\Departures\get_cards_data( [ 21001, 21000 ], 'USD' ), // TODO: Replace with cards data from block.
	'currency'        => quark_get_template_data( 'default_currency', 'USD' ),
	'filters_data'    => Quark\Theme\Search_Filters\get_filters_for_sidebar_search(),
] )

<x-expedition-search>
	{{-- Header --}}
	<x-expedition-search.header>
		<x-expedition-search.result-count count="{{ $results_count }}" />
		<x-expedition-search.filters :currency="$currency" />
	</x-expedition-search.header>

	{{-- Results --}}
	<x-expedition-search.body>
		<x-sidebar-grid>
			<x-sidebar-grid.content>
				<x-expedition-search.selected-filters />
				<x-expedition-search.recent-searches />
				<x-expedition-search.results :count="$remaining_count">
					<x-parts.expedition-search-result-cards :cards="$cards ?? []" />
				</x-expedition-search.results>
			</x-sidebar-grid.content>

			{{-- Filters --}}
			<x-sidebar-grid.sidebar :sticky="true" :show_on_mobile="false" sidebar_position="left">
				@if ( ! empty( $filters_data ) )
					<x-expedition-search.sidebar-filters :filters_data="$filters_data" />
				@endif
			</x-sidebar-grid.sidebar>
		</x-sidebar-grid>
	</x-expedition-search.body>
</x-expedition-search>
