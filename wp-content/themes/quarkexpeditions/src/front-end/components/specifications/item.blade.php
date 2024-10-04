@props( [
	'label' => '',
	'value' => '',
] )

@php
	if ( empty( $label ) || ! isset( $value ) || '' === $value ) {
		return;
	}
@endphp

<div class="specifications__item">
	<div class="specifications__label">
		<x-escape :content="$label" />
	</div>
	<div class="specifications__value h5">
		<x-escape :content="$value" />
	</div>
</div>
