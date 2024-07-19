@props( [
	'label' => '',
	'value' => '',
] )

@php
	if ( empty( $label ) || empty( $value ) ) {
		return;
	}
@endphp

<div class="media-detail-cards__detail-item">
	<div class="media-detail-cards__detail-label body-small">
		<x-escape :content="$label" />
	</div>
	<div class="media-detail-cards__detail-value">
		<x-escape :content="$value" />
	</div>
</div>
