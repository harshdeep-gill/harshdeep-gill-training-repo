@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<div class="footer__column">
	<x-maybe-link href="{{ $url }}" class="footer__column-link">
		{!! $slot !!}
	</x-maybe-link>
</div>
