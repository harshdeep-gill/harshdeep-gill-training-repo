@props( [
	'text' => '',
	'url'  => '',
	'icon' => 'shield',
] )

@php
	if ( empty( $text ) ) {
		return;
	}
@endphp

<div class="expedition-cards__card-banner overline">
	<x-maybe-link href="{{ $url }}" class="expedition-cards__card-banner-link" fallback_tag="div">
		<x-svg name="{{ $icon }}" />
		<span><x-escape :content="$text" /></span>
	</x-maybe-link>
</div>
