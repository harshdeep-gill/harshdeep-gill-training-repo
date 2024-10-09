@props( [
	'id' => '',
] )

@php
	if ( empty( $id ) || empty( $slot ) ) {
		return;
	}
@endphp

<x-modal class="product-options-cards__modal" id="{{ $id }}">
	{!! $slot !!}
</x-modal>
