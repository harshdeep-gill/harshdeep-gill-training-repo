@props( [
	'id'    => '',
	'class' => '',
	'open'  => false,
] )

@php
	if ( empty( $slot ) || empty( $id ) ) {
		return;
	}

	// Classes.
	$classes = [ 'form-request-quote__tab' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<tp-tabs-tab
	@class( $classes )
	id="{{ $id }}" {!! $open ? "open='yes'" : '' !!}
	>
	{!! $slot !!}
</tp-tabs-tab>
