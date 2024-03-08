@props( [
	'color'         => '',
	'class'         => '',
	'href'          => '',
	'target'        => '',
	'appearance'    => '',
	'type'          => '',
	'size'          => '',
	'variant'       => '',
	'icon'          => '',
	'icon_position' => 'left',
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

	if ( ! empty( $icon ) ) {
		$classes[] = 'btn--has-icon';
	}

	if ( ! empty( $variant ) && 'media' === $variant ) {
		$classes[] = 'btn--media';
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

			{!! $slot !!}

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

			{!! $slot !!}

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
