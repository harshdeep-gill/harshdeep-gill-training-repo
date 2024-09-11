@props( [
	'destinations' => [],
] )

@php
	if ( empty( $destinations ) ) {
		return;
	}
@endphp

<quark-search-filters-bar-destinations class="search-filters-bar__destinations" selected="false">
	<div class="search-filters-bar__destinations-content">
		<div class="search-filters-bar__destinations-label body-small">{{ __( 'Destinations', 'qrk' ) }}</div>
		<div class="search-filters-bar__destinations-placeholder">{{ __( 'Anywhere', 'qrk' ) }}</div>
	</div>
	<div class="search-filters-bar__destinations-icon">
		<x-svg name="chevron-left" />
	</div>
</quark-search-filters-bar-destinations>