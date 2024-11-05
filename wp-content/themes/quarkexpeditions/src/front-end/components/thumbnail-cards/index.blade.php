@props( [
	'is_carousel' => false,
	'full_width'  => true,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'thumbnail-cards' ];

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section
	@class( $classes )
	:$full_width
	:wrap="true"
>
	<x-thumbnail-cards.carousel :is_carousel="$is_carousel">
		{!! $slot !!}
	</x-thumbnail-cards.carousel>
</x-section>
