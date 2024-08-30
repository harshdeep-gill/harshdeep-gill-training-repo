@props( [
	'url' => '',
] )

@php
	if ( empty( $url ) ) {
		return;
	}
@endphp

<x-button href="{{ $url }}" size="big" class="options-button__default-option">
	{!! $slot !!}
</x-button>
