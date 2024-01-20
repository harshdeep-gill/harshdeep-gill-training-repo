@props( [
	'rating'  => '',
] )

@php
	if ( empty( $rating ) ) {
		return;
	}
@endphp

@php
	$rating_style = sprintf( '--rating: %f;', floatval( $rating ) );
@endphp

<span class="rating-stars" style="{!! esc_attr( $rating_style ) !!}"></span>