@props( [
	'current_page' => 1,
	'total_pages'  => 1,
] )

@php
	if ( $current_page < 1 || $current_page > $total_pages ) {
		return;
	}
@endphp

<x-pagination>
	<x-pagination.items-per-page />
	<x-pagination.total-pages :current_page="$current_page" :total_pages="$total_pages" />
	<x-pagination.links>
		<x-dates-rates.pagination.links :current_page="$current_page" :total_pages="$total_pages" />
	</x-pagination.links>
</x-pagination>
