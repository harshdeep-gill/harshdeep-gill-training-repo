@props( [
	'label' => '',
	'value' => '',
	'url'   => '',
] )

<x-maybe-link href="{{ $url }}" fallback_tag="div" class="media-content-card__content-info">
	<span class="media-content-card__content-info-label"><x-escape :content="$label" /></span>
	<span class="media-content-card__content-info-value"><x-escape :content="$value" /></span>
</x-maybe-link>
