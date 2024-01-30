@php
	if( empty( $slot ) ) {
		return;
	}

	$classes = [ 'icon-info-columns' ];
@endphp


<x-section @class( $classes )>
	{!! $slot !!}
</x-section>
