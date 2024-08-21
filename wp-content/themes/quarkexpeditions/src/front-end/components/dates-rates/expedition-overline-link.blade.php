@props( [
	'title' => '',
	'url'   => '',
] )

@php
	if ( empty( $title ) ) {
		return;
	}
@endphp

<x-maybe-link href="{{ $url }}" class="dates-rates__expedition-overline-link" fallback_tag="span">
	<x-escape :content="$title" />
</x-maybe-link>
