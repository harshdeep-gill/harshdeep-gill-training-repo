@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}

	$classes = [ 'info-cards__card-cta' ];
@endphp

<div @class( $classes )>
	<span class="info-cards__card-cta-text">
		<x-escape :content="$text"/>
	</span>

	<span class="info-cards__card-cta-icon">
		<x-svg name="chevron-left"/>
	</span>
</div>
