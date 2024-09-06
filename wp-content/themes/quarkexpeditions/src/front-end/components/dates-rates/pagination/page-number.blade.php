@props( [
	'current' => false,
	'number'  => 0,
] )

@php
	if ( empty( $slot ) || empty( $number ) || 0 === $number ) {
		return;
	}

	$classes = [ 'page-numbers' ];

    if ( ! empty( $current ) ) {
		$classes[] = 'current';
	}
@endphp

<quark-dates-rates-pagination-page-number number="{!! esc_attr( $number ) !!}">
	<button @class( $classes )><x-content :content="$slot" /></button>
</quark-dates-rates-pagination-page-number>
