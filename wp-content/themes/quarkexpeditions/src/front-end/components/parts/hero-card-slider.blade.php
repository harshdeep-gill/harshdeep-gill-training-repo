@props( [
	'interval'        => 5,
	'show_controls'   => true,
	'transition_type' => false,
	'items'           => [],
	'is_lightbox'     => false,
] )

@php
if ( empty( $items ) ) {
	return;
}
@endphp

<x-hero-card-slider :arrows="$show_controls" :interval="$interval" :auto_slide="'auto' === $transition_type">
	@foreach ( $items as $item )
		<x-hero-card-slider.card>
			<x-hero-card-slider.image image_id="{{ $item['image_id'] }}" :is_lightbox="$is_lightbox" />
		</x-hero-card-slider.card>
	@endforeach
</x-hero-card-slider>
