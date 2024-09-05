@props( [
	'prev'    => false,
	'next'    => false,
	'current' => false,
	'number'  => 1,
] )

@php
	if (
		empty( $slot ) ||
		( empty( $number ) && empty( $prev ) && empty( $next ) )
	) {
		return;
	}

	$classes = [ 'page-numbers' ];

	if ( ! empty( $prev ) ) {
		$classes[] = 'prev';
	} elseif ( ! empty( $next ) ) {
		$classes[] = 'next';
	} elseif ( $current ) {
		$classes[] = 'current';
	}
@endphp

<quark-dates-rates-pagination-page-number number="{!! esc_attr( $number ) !!}">
	<button @class( $classes )><x-content :content="$slot" /></button>
</quark-dates-rates-pagination-page-number>
