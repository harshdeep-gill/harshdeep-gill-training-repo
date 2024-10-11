@props( [
	'links' => [],
	'class' => '',
] )

@php
	$classes = [ 'social-links' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	@foreach ( $links as $type => $url )
		@if ( empty( $url ) || empty( $type ) )
			@continue
		@endif
		<a href="{{ $url }}" class="social-links__link" title="{{ $type }}" target="_blank" rel="nofollow noopener noreferrer">
			<x-svg name="social/{{ $type }}" />
		</a>
	@endforeach
</div>
