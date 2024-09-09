@props( [
	'current_page' => 1,
	'total_pages'  => 1,
] )

@php
	if (
		empty( $current_page ) ||
		empty( $total_pages ) ||
		$current_page < 1 ||
		$current_page > $total_pages
	) {
		return;
	}

@endphp

<x-pagination>
	<x-dates-rates.pagination.items-per-page />
	<x-dates-rates.pagination.total-pages :current_page="$current_page" :total_pages="$total_pages" />
	<x-pagination.links>
		<x-dates-rates.pagination.links :current_page="$current_page" :total_pages="$total_pages" />
	</x-pagination.links>
</x-pagination>
