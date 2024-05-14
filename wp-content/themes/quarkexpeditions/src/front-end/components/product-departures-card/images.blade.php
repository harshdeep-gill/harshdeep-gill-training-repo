@props( [
	'image_ids' => [],
] )

@php
	// Return if the image ids empty.
	if ( empty( $image_ids ) || ! is_array( $image_ids ) ) {
		return;
	}
@endphp

<div class="product-departures-card__images-wrap">
	<div class="product-departures-card__images">
		@foreach( $image_ids as $image_id )
			<x-product-departures-card.image image_id="{{ $image_id }}" />
		@endforeach
	</div>

	{!! $slot !!}
</div>
