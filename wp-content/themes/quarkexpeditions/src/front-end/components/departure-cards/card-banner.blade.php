@props( [
	'text' => '',
	'url'  => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="departure-cards__card-banner overline">
	<x-maybe-link href="{{ $url }}" class="departure-cards__card-banner-link">
		<x-svg name="shield" />
		<span><x-escape :content="$text" /></span>
	</x-maybe-link>
</div>
