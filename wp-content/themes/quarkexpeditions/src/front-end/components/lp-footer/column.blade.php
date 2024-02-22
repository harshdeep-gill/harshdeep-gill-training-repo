@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-maybe-link
	href="{{ $url }}"
	fallback_tag="div"
	class="lp-footer__column"
>
	{!! $slot !!}
</x-maybe-link>
