@props( [
	'label' => '',
	'value' => '',
	'url'   => '',
	'target' => '',
] )

<x-maybe-link
	href="{{ $url }}"
	fallback_tag="div"
	class="media-content-card__content-info"
	target="{{ $target }}"
>
	<span class="media-content-card__content-info-label"><x-escape :content="$label" /></span>
	<strong class="media-content-card__content-info-value"><x-escape :content="$value" /></strong>
</x-maybe-link>
