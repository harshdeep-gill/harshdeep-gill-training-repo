@props( [
	'id'     => '',
	'active' => false,
] )
@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-tabs-nav-item class="itinerary-details__tabs-nav-item" {!! $active ? "active='yes'" : '' !!}>
	<a class="itinerary-details__tabs-nav-link" href="#{{ $id }}">
		{!! $slot !!}
	</a>
</tp-tabs-nav-item>
