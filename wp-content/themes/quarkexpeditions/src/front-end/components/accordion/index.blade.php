@props( [
	'class'       => '',
	'id'          => '',
	'full_border' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'accordion typography-spacing' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	if ( true === $full_border ) {
		$classes[] = 'accordion--full-border';
	}

	quark_enqueue_style( 'tp-accordion' );
	quark_enqueue_script( 'tp-accordion' );
@endphp

<tp-accordion
	@class( $classes )
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
>
	{!! $slot !!}
</tp-accordion>
