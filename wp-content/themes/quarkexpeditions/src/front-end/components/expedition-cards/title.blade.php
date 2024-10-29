@props( [
	'href' => '',
	'slot' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<h3 class="expedition-cards__title h4">
	<x-maybe-link :href="$href">
		<x-content :content="$slot" />
	</x-maybe-link>
</h3>
