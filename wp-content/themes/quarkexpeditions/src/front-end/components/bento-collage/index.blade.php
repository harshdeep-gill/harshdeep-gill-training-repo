@props( [
	'layout'            => 'grid',
	'mobile_carousel'   => true,
	'carousel_overflow' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'bento-collage' ];

	if ( true === $carousel_overflow ) {
		$classes[] = 'bento-collage--has-overflow';
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section
	@class( $classes )
	:full_width="true"
>
	<x-bento-collage.carousel :layout="$layout" :mobile_carousel="$mobile_carousel">
		{!! $slot !!}
	</x-bento-collage.carousel>
</x-section>
