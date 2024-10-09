@props( [
	'title' => __( 'Where to?', 'qrk' ),
] )
@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="search-filters-bar__modal-open-container">
	<div class="search-filters-bar__modal-open-container-title">
		<x-escape :content="$title" />
	</div>
	<div class="search-filters-bar__modal-open-container-content">
		{!! $slot !!}
	</div>
</div>