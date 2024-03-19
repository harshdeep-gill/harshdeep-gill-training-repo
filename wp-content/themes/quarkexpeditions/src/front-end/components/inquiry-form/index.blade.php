@props( [
	'class'          => '',
	'thank_you_page' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'inquiry-form' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<quark-inquiry-form @class( $classes )>
	{!! $slot !!}
</quark-inquiry-form>
