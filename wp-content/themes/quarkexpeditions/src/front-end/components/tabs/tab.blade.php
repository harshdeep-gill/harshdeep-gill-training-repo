@props( [
	'id' => '',
] )

@php
	if ( empty( $slot ) || empty( $id ) ) {
		return;
	}
@endphp

<tp-tabs-tab class="tabs__tab" id="{{ $id }}">
	{!! $slot !!}
</tp-tabs-tab>
