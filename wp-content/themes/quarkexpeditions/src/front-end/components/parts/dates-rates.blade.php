@props( [
	'filter_data' => [],
] )

@php
	if ( empty( $filter_data ) ) {
		return;
	}
@endphp

<x-dates-rates>
	<x-dates-rates.header>
		<x-dates-rates.filters :filter_data="$filter_data" />
		<x-dates-rates.results.count />
	</x-dates-rates.header>
	<x-dates-rates.results />
	<x-dates-rates.pagination />
</x-dates-rates>
