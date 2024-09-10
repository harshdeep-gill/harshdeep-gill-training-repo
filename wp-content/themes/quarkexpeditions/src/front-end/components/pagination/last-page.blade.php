@props( [
	'href'  => '',
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'pagination__last-page' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-maybe-link
	@class( $classes )
	:href="$href"
	fallback_tag="button"
>
	{!! $slot !!}
</x-maybe-link>
