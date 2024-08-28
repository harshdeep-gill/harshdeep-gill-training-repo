@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<li class="options-button__option">
	<x-maybe-link href="{{ $url }}">
		{!! $slot !!}
	</x-maybe-link>
</li>
