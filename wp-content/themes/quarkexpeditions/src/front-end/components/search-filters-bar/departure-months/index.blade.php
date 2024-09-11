@props( [
	'available_months' => [],
] )

@php
	if ( empty( $available_months ) ) {
		return;
	}
@endphp

<quark-search-filters-bar-departure-months
	class="search-filters-bar__departure-months"
	available-months="{{ wp_json_encode( $available_months ) }}"
	selected="false"
>
	<div class="search-filters-bar__departure-months-content">
		<div class="search-filters-bar__departure-months-label body-small">{{ __( 'Departures', 'qrk' ) }}</div>
		<div class="search-filters-bar__departure-months-placeholder">{{ __( 'Any time', 'qrk' ) }}</div>
	</div>
	<div class="search-filters-bar__departure-months-icon">
		<x-svg name="chevron-left" />
	</div>
</quark-search-filters-bar-departure-months>
