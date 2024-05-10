@props( [
	'text' => '',
	'url'  => '',
] )

@php
	if ( empty( $text ) || empty( $url ) ) {
		return;
	}

	$classes = [ 'info-cards__card-cta' ];
@endphp

<a
	@class($classes)
	href="{!! esc_url( $url ) !!}"
>
	<span class="info-cards__card-cta-text">
		<x-escape :content="$text"/>
	</span>

	<span class="info-cards__card-cta-icon">
		<x-svg name="chevron-left"/>
	</span>
</a>
