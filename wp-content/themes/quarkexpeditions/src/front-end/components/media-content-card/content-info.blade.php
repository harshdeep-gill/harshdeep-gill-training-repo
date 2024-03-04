@props( [
	'label' => '',
	'value' => '',
	'url'   => '',
] )

<a href="{{ $url }}" class="media-content-card__content-info">
	<span class="media-content-card__content-info-label"><x-escape :content="$label" /></span>
	<span class="media-content-card__content-info-value"><x-escape :content="$value" /></span>
</a>


