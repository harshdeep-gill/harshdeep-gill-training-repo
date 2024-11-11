@props( [
	'align'             => 'left',
	'layout'            => 'carousel',
	'carousel_overflow' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'product-cards' ];

	if ( ! empty( $align ) && 'grid' === $layout && 'center' === $align ) {
		$classes[] = 'product-cards--align-center';
	}

	if ( true === $carousel_overflow ) {
		$classes[] = 'product-cards--has-overflow';
	}

	if ( ! empty( $layout ) ) {
		$classes[] = sprintf( 'product-cards--%s', $layout );
	}

	quark_enqueue_style( 'tp-slider' );
	quark_enqueue_script( 'tp-slider' );
@endphp

<x-section
	@class( $classes )
	:full_width="true"
	:wrap="true"
>
	<x-product-cards.carousel :layout="$layout">
		{!! $slot !!}
	</x-product-cards.carousel>
</x-section>
