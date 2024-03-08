@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<article class="product-cards__card">
	{!! $slot !!}
</article>
