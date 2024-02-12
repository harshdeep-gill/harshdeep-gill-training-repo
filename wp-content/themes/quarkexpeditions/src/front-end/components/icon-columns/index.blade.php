@props( [
	'variant' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'icon-columns' ];

	if ( ! empty( $variant ) ) {
		$variant_modifier = match ($variant) {
			'light' => 'light',
			'dark'  => 'dark',
			default => ''
		};

		if ( ! empty( $variant_modifier ) ) {
			$classes[] = 'icon-columns--' . $variant_modifier;
		}
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
