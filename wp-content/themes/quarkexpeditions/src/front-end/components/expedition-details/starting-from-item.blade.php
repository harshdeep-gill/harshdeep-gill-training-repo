@props( [
	'title' => '',
	'url'   => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<li class="expedition-details__starting-from-item">
	<x-maybe-link
		href="{{ $url }}"
		class="expedition-details__starting-from-item-link"
	>
		<x-escape :content="$title"/>
	</x-maybe-link>
</li>
