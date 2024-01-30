@props( [
	'classes'  => [],
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
