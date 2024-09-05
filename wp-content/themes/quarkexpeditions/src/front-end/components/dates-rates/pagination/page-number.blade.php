@props( [
	'prev'    => false,
	'next'    => false,
	'current' => false,
	'number'  => 0,
] )

@php
	if (
		empty( $slot ) ||
		( ( empty( $number ) || 0 === $number ) && empty( $prev ) && empty( $next ) )
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

<quark-dates-rates-pagination-page-number
	@if ( 0 !== $number )
		number="{!! esc_attr( $number ) !!}"
	@endif
>
	<button @class( $classes )><x-content :content="$slot" /></button>
</quark-dates-rates-pagination-page-number>
