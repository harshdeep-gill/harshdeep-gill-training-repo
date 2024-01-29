@php
	if( empty( $slot ) ) {
		return;
	}

	$classes = [ 'icon-info-columns' ];
@endphp


<div @class( $classes )>
	{!! $slot !!}
</div>
