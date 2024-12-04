@props( [
	'text' => '',
] )

@php
	if ( empty( $text ) ) {
		return;
	}

	$classes = [ 'bento-collage__card-cta' ];
@endphp

<div @class( $classes )>
	<span class="bento-collage__card-cta-text">
		<x-escape :content="$text"/>
	</span>

	<span class="bento-collage__card-cta-icon">
		<x-svg name="chevron-left"/>
	</span>
</div>
