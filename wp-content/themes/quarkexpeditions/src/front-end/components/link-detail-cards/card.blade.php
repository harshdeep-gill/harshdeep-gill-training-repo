@props( [
	'url'    => '',
	'target' => '',
] )

@php
	if ( empty( $slot ) || empty( $url ) ) {
		return;
	}
@endphp

<a
	class="link-detail-cards__card"
	href="{!! esc_url( $url ) !!}"
	@if ( ! empty( $target ) )
		target="{{ $target }}"
	@endif
>
	{!! $slot !!}
</a>
