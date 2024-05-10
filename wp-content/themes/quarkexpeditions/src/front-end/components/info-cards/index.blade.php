@props( [
	'layout' => 'grid',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards' ];

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section
	@class( $classes )
	:full_width="true"
>
	<x-info-cards.carousel :layout="$layout">
		{!! $slot !!}
	</x-info-cards.carousel>
</x-section>
