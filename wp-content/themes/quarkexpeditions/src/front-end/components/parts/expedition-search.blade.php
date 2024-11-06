@props( [
	'result_count'    => 0,
	'remaining_count' => 0,
	'cards'           => [],
	'currency'        => '',
	'filters_data'    => [],
	'page'            => 1,
	'next_page'        => 1,
] )

@php
	$destinations = $filters_data['destinations'] ?? [];
	$available_months = $filters_data['months'] ?? [];

	$filters_api_url = quark_get_template_data( 'filters_api_url' );
	$search_page_url = quark_get_template_data( 'search_page_url' );

@endphp

<x-expedition-search>
	{{-- Header --}}
	<x-expedition-search.header>
		<x-expedition-search.result-count count="{{ $result_count }}" />
		<x-expedition-search.filters :currency="$currency" />
	</x-expedition-search.header>

	{{-- Results --}}
	<x-expedition-search.body>
		<x-sidebar-grid>
			<x-sidebar-grid.content>
				<x-expedition-search.selected-filters />
				<x-expedition-search.recent-searches />
				<x-expedition-search.results :remaining_count="$remaining_count" :result_count="$result_count" :page="$page" :next_page="$next_page">
					<x-parts.expedition-search-result-cards :cards="$cards ?? []" />
				</x-expedition-search.results>
			</x-sidebar-grid.content>

			{{-- Filters --}}
			<x-sidebar-grid.sidebar :sticky="true" :show_on_mobile="false" sidebar_position="left" :scroll="false">
				@if ( ! empty( $filters_data ) )
					<x-expedition-search.sidebar-filters :filters_data="$filters_data" />
				@endif
			</x-sidebar-grid.sidebar>
		</x-sidebar-grid>
	</x-expedition-search.body>
</x-expedition-search>
