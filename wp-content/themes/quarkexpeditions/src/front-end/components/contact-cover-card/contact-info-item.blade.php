@props( [
	'label' => '',
	'value' => '',
	'url'   => '',
] )

@php
	if ( empty( $url ) || empty( $value ) || empty( $label ) ) {
		return;
	}
@endphp

<div class="contact-cover-card__contact-info-item">
	<x-button href="{{ $url }}" size="big" color="black">
		<span class="contact-cover-card__contact-info-item-label"><x-escape :content="$label" /></span>
		<span class="contact-cover-card__contact-info-item-value"><x-escape :content="$value" /></span>
	</x-button>
</div>
