@props( [
	'url'        => '',
	'new_window' => false,
] )

@php
	if ( empty( $slot ) || empty( $url ) ) {
		return;
	}
@endphp

<a
	href="{!! esc_url( $url ) !!}"
	class="section__heading-link"

	@if ( ! empty( $new_window ) )
		target="_blank"
	@endif
>
	<x-content :content="$slot" />
</a>
