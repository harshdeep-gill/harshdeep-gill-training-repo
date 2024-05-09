@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'info-cards__card-content' ];
@endphp

<div @class($classes)>
	{!! $slot !!}
</div>
