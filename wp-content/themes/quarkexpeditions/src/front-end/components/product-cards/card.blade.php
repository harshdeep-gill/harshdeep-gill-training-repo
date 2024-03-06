@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="product-cards__card">
	{!! $slot !!}
	<x-maybe-link href="{{ $url }}" class="product-cards__link"></x-maybe-link>
</article>
