@props( [
	'rating' => '5',
] )

@php
	if ( empty( $rating ) ) {
		return;
	}
@endphp

<div class="expedition-cards__rating">
	<x-rating-stars rating="{{ $rating }}" />

	<div class="expedition-cards__rating-content">
		{!! $slot !!}
	</div>
</div>
