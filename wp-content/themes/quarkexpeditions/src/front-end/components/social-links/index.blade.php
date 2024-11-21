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
		@if ( empty( $url ) || empty( $url['link'] ) || empty( $type ) )
			@continue
		@endif
		<a
			href="{{ $url['link'] }}"
			class="social-links__link"
			title="{{ $type }}"
			rel="nofollow noopener noreferrer"
			@if ( ! empty( $url['target'] ) )
				target="{{ $url['target'] }}"
			@endif
		>
			<x-svg name="social/{{ $type }}" />
		</a>
	@endforeach
</div>
