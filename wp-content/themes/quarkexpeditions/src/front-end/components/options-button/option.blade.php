@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<li class="options-button__option">
	<x-maybe-link href="{{ $url }}" class="options-button__option-link">
		{!! $slot !!}
	</x-maybe-link>
</li>
