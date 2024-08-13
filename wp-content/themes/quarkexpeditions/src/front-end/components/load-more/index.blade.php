@props( [
	'partial'        => '',
	'selector'       => '',
	'load_more_text' => __( 'Load More', 'qrk' ),
	'payload'        => [],
] )

@php
	// Early return.
	if (
		empty( $selector ) ||
		empty( $partial ) ||
		empty( $slot )
	) {
		return;
	}
@endphp

<quark-load-more
	class="load-more typography-spacing"
	loading="false"
	partial="{{ $partial }}"
	selector="{{ $selector }}"
	payload = {{ wp_json_encode( $payload ) }}
>
	{!! $slot !!}
	<x-load-more.load-more-button :load_more_text="$load_more_text" />
</quark-load-more>
