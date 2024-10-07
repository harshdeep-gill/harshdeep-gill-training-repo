@props( [
	'filter_data'    => [],
	'result_count'   => 0,
	'cards'          => [],
	'page'           => 1,
	'per_page'       => 4,
	'currency'       => quark_get_template_data( 'default_currency', 'USD' ),
] )

@php
	if (
		empty( $filter_data ) ||
		! isset( $result_count ) ||
		! isset( $page ) ||
		! isset( $per_page ) ||
		! is_array( $cards ) ||
		1 > $per_page
	) {
		return;
	}
@endphp

<x-dates-rates>
	<x-dates-rates.header>
		<x-dates-rates.filters :filter_data="$filter_data" :currency="$currency" />
		<x-dates-rates.results.count />
	</x-dates-rates.header>
	<x-dates-rates.results
		page="{{ $page }}"
		total_pages="{{ ceil( $result_count / $per_page ) }}"
		per_page="{{ $per_page }}"
		result_count="{{ $result_count }}"
	>
		<x-parts.dates-rates-cards :cards="$cards" />
	</x-dates-rates.results>
	<x-dates-rates.pagination />
</x-dates-rates>
