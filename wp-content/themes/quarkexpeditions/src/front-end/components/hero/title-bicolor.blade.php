@props( [
	'white_text'     => '',
	'yellow_text'    => '',
	'switch_colors'   => false,
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
	@if ( ! empty( $switch_colors ) )
		<span class="hero__title--color-yellow">
			<x-content :content="$yellow_text" />
		</span>
		<span class="hero__title--color-white">
			<x-content :content="$white_text" />
		</span>
	@else
		<span class="hero__title--color-white">
			<x-content :content="$white_text" />
		</span>
		<span class="hero__title--color-yellow">
			<x-content :content="$yellow_text" />
		</span>
	@endif
</h1>
