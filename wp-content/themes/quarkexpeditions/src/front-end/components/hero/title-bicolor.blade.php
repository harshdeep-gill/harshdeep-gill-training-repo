@props( [
	'white_text'     => '',
	'yellow_text'    => '',
	'yellow_first'   => false,
	'use_promo_font' => false,
] )

@php
	if ( empty( $white_text ) || empty( $yellow_text ) ) {
		return;
	}

	$classes = [ 'hero__title', 'hero__title--bicolor' ];

	if ( ! empty( $use_promo_font ) ) {
		$classes[] = 'font-family--promo';
	}
@endphp

<h1 @class( $classes )>
	@if ( ! empty( $yellow_first ) )
		<span class="hero__title--yellow-text">
			<x-content :content="$yellow_text" />
		</span>
		<span class="hero__title--white-text">
			<x-content :content="$white_text" />
		</span>
	@else
		<span class="hero__title--white-text">
			<x-content :content="$white_text" />
		</span>
		<span class="hero__title--yellow-text">
			<x-content :content="$yellow_text" />
		</span>
	@endif
</h1>
