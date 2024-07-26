@props( [
	'desktop_carousel' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes         = [ 'media-description-cards' ];
	$section_classes = [ 'media-description-cards__section' ];

	if ( true === $desktop_carousel ) {
		$classes[]         = 'media-description-cards--desktop-carousel';
		$section_classes[] = 'media-description-cards__section--desktop-carousel';
	}

	// TP Slider.
	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section @class( $section_classes )>
	<div @class( $classes )>
		<x-media-description-cards.carousel>
			{!! $slot !!}
		</x-media-description-cards.carousel>
	</div>
</x-section>
