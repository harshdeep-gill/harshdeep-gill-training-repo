@props( [
	'url' => '',
] )

@php
	if ( empty( $slot ) || empty( $url ) ) {
		return;
	}
@endphp

<a
	href="{!! esc_url( $url ) !!}"
	class="section__heading-link"
>
	<x-content :content="$slot" />
</a>
