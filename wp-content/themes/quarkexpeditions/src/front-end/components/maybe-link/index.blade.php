@props( [
	'href'         => '',
	'class'        => '',
	'target'       => '',
	'fallback_tag' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$tag_name = 'a';

	if ( empty( $href ) && ! empty( $fallback_tag ) ) {
		$tag_name = $fallback_tag;
	}

	$classes = [ 'maybe-link' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

@if ( ! empty( $href ) || ! empty( $fallback_tag ) )
	<{{ $tag_name }}
		@class( $classes )
		@if ( ! empty( $href ) )
			href="{{ $href }}"
		@endif
		@if ( ! empty( $target ) )
			target="{{ $target }}"
		@endif
	>
@endif

	{!! $slot !!}

@if ( ! empty( $href ) || ! empty( $fallback_tag ) )
	</{{ $tag_name }}>
@endif
