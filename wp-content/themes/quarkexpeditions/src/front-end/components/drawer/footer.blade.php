@props( [
	'class' => '',
] )

@php
	if( empty( $slot ) ) {
		return;
	}
@endphp

<footer @class( [ $class, 'drawer__footer' ] )>
	{!! $slot !!}
</footer>
