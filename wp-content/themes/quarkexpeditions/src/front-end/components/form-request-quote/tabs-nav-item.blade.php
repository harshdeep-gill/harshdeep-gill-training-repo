@props( [
	'id'     => '',
	'active' => false,
] )
@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<tp-tabs-nav-item class="form-request-quote__tabs-nav-item" {!! $active ? "active='yes'" : '' !!}>
	<a class="form-request-quote__tabs-nav-link" href="#{{ $id }}">
		{!! $slot !!}
	</a>
</tp-tabs-nav-item>
