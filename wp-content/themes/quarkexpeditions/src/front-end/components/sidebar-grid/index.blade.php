@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'sidebar-grid', 'grid' ];
@endphp

<div @class( $classes )>
	{!! $slot !!}
</div>
