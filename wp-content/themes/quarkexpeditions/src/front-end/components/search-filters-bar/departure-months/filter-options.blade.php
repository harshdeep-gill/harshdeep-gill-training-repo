@props( [
	'available_months' => [],
] )

@php
	if ( empty( $available_months ) ) {
		return;
	}
@endphp

<quark-search-filters-bar-departure-months-filter-options
	class="search-filters-bar__departure-months-filter-options"
	available-months="{{ wp_json_encode( $available_months ) }}"
>

</quark-search-filters-bar-departure-months-filter-options>