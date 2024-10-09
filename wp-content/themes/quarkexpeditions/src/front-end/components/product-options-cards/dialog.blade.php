@props( [
	'id' => '',
] )

@php
	if ( empty( $id ) || empty( $slot ) ) {
		return;
	}
@endphp

<x-dialog class="product-options-cards__dialog" id="{{ $id }}">
	{!! $slot !!}
</x-dialog>
