@props( [
	'color'      => '',
	'class'      => '',
	'href'       => '',
	'target'     => '',
	'appearance' => '',
	'type'       => '',
	'size'       => '',
] )

@php
	$classes = [ 'btn' ];

	if ( ! empty( $color ) ) {
		$classes[] = sprintf( 'btn--color-%s', $color );
	}

	if ( ! empty( $appearance ) && 'outline' === $appearance ) {
		$classes[] = 'btn--outline';
	}

	if ( ! empty( $size ) ) {
		$classes[] = sprintf( 'btn--size-%s', $size );
	}

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$classes = implode( ' ', $classes );
@endphp

@if ( ! empty( $href ) )
	<a href="{{ $href }}"
	   class="{{ $classes }}"
	   @if ( ! empty( $target ) )
		   target="{{ $target }}"
	   @endif
	>

		{{ $slot }}
	</a>
@else
	<button
		class="{{ $classes }}"
		@if ( ! empty( $type ) )
			type="{{ $type }}"
		@endif
		{{ $attributes->filter( fn ( $value, $key ) => ! in_array( $key, [ 'color', 'class' ], true ) )->merge() }}
	>
		{{ $slot }}
	</button>
@endif
