@props( [
	'class' => '',
	'text'  => '',
] )

@php
	$classes = [ 'hero-card-slider__cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp


<div @class( $classes )>
	<span class="hero-card-slider__cta-text">
		<x-escape :content="$text" />
	</span>
	<span class="hero-card-slider__cta-icon">
		<x-svg name="chevron-left"/>
	</span>
</div>
