@props( [
	'color'         => '',
	'class'         => '',
	'href'          => '',
	'target'        => '',
	'appearance'    => '',
	'type'          => '',
	'icon'          => '',
	'icon_position' => 'left',
] )

@php
	$classes = [ 'btn' ];

	if ( empty( $color ) && ! empty( $appearance ) && 'outline' === $appearance ) {
		$classes[] = sprintf( 'btn--outline' );
	} elseif ( ! empty( $color ) && ! empty( $appearance ) && 'outline' === $appearance ) {
		$classes[] = sprintf( 'btn--%s-outline', $color ?? '' );
	} elseif ( ! empty( $appearance ) && 'unstyled' === $appearance ) {
		$classes[] = 'btn--unstyled';
	} elseif ( ! empty( $color ) ) {
		$classes[] = sprintf( 'btn--%s', $color );
	}

	if ( ! empty( $icon ) ) {
		$classes[] = 'btn--has-icon';
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
		{{-- Add icon if position is left --}}
		@if ( 'left' === $icon_position && ! empty( $icon ) )
			<span class="btn__icon btn__icon-left">
				<x-svg :name="$icon" />
			</span>
		@endif

		{{-- Add text wrapper if icon not empty --}}
		@if ( ! empty( $icon ) )
			<span class="btn__content">
		@endif

			{{ $slot }}

		{{-- Close - Added text wrapper if icon not empty --}}
		@if ( ! empty( $icon ) )
			</span>
		@endif

		{{-- Add icon if position is right --}}
		@if ( 'right' === $icon_position && ! empty( $icon ) )
			<span class="btn__icon btn__icon-right">
				<x-svg :name="$icon" />
			</span>
		@endif
	</a>
@else
	<button
		class="{{ $classes }}"
		@if ( ! empty( $type ) )
			type="{{ $type }}"
		@endif
		{{ $attributes->filter( fn ( $value, $key ) => ! in_array( $key, [ 'color', 'class' ], true ) )->merge() }}
	>
		{{-- Add icon if position is left --}}
		@if ( 'left' === $icon_position && ! empty( $icon ) )
			<span class="btn__icon btn__icon-left">
				<x-svg :name="$icon" />
			</span>
		@endif

		{{-- Add text wrapper if icon not empty --}}
		@if ( ! empty( $icon ) )
			<span class="btn__content">
		@endif

			{{ $slot }}

		{{-- Close - Added text wrapper if icon not empty --}}
		@if ( ! empty( $icon ) )
			</span>
		@endif

		{{-- Add icon if position is right --}}
		@if ( 'right' === $icon_position && ! empty( $icon ) )
			<span class="btn__icon btn__icon-right">
				<x-svg :name="$icon" />
			</span>
		@endif
	</button>
@endif
