@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div @class( [ $class, 'drawer__body' ] ) >
	{!! $slot !!}
</div>
