@props( [
	'layout'            => 'grid',
	'mobile_carousel'   => true,
	'carousel_overflow' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards' ];

	if ( true === $carousel_overflow ) {
		$classes[] = 'info-cards--has-overflow';
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section
	@class( $classes )
	:full_width="true"
>
	<x-info-cards.carousel :layout="$layout" :mobile_carousel="$mobile_carousel">
		{!! $slot !!}
	</x-info-cards.carousel>
</x-section>
