@props( [
	'id' => '',
] )

@php
	if ( empty( $id ) || empty( $slot ) ) {
		return;
	}

	$has_this_rendered = quark_has_this_rendered_once( $id );

	if ( $has_this_rendered ) {
		return;
	}
@endphp

{!! $slot !!}
