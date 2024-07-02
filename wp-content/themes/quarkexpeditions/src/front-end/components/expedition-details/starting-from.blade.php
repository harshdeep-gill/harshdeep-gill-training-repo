@props( [
	'title' => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<div class="expedition-details__starting-from">
	<p class="expedition-details__starting-from-label">{{ __( 'Starting from', 'qrk' ) }}</p>
	<p class="expedition-details__starting-from-title">
		<x-escape content="{{ $title }}"/>
	</p>
</div>
