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

<quark-site-banner class="site-banner full-width">
	<x-content :content="$message" />
</quark-site-banner>
