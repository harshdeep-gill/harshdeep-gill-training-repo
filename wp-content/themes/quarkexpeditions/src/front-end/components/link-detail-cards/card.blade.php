@props( [
	'class' => '',
	'url'   => '',
] )

@php
	if ( empty( $slot ) || empty( $url ) ) {
		return;
	}

	$classes = [ 'link-detail-cards__card' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<a
	@class( $classes )
	href="{!! esc_url( $url ) !!}"
>
	{!! $slot !!}
</a>
