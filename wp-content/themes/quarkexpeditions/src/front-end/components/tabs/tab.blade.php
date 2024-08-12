@props( [
	'id'   => '',
	'open' => false,
] )

@php
	if ( empty( $slot ) || empty( $id ) ) {
		return;
	}
@endphp

<tp-tabs-tab class="tabs__tab" id="{{ $id }}" {!! $open ? "open='yes'" : '' !!}>
	{!! $slot !!}
</tp-tabs-tab>
