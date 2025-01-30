@props( [
	'display_banner' => false,
	'message'        => '',
] )

@php
	if( empty( $message ) || empty( $display_banner ) ) {
		return;
	}

	$animation_duration = str_word_count( wp_strip_all_tags( $message ) );

	if ( 0 === $animation_duration ) {
		return;
	}
@endphp

<div class="site-banner full-width" style="--site-banner-animation-duration:{{ $animation_duration }}s;">
	<div class="site-banner__track">
		<div class="site-banner__content">
			<span class="site-banner__slide">
				<x-content :content="$message" />
			</span>
			<span class="site-banner__slide">
				<x-content :content="$message" />
			</span>
		</div>
	</div>
</div>
