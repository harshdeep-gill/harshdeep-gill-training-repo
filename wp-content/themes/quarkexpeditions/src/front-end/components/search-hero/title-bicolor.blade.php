@props( [
	'use_promo_font' => false,
	'yellow_text'    => '',
	'white_text'     => '',
] )

@php
	if( empty( $white_text ) || empty( $yellow_text ) ) {
		return;
	}
	$classes = [ 'search-hero__title', 'search-hero__title--bicolor' ];

	if ( ! empty( $use_promo_font ) ) {
		$classes[] = 'font-family--promo';
	}
@endphp

<h1 @class( $classes )>
	<span class="search-hero__title--white-text">
		<x-content :content="$white_text" />
	</span>
	<span class="search-hero__title--yellow-text">
		<x-content :content="$yellow_text" />
	</span>
</h1>
