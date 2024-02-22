@props( [
	'title' => '',
	'light' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'season-highlights__item' ];

	if ( true === $light ) {
		$classes[] = 'season-highlights__item--light';
	}
@endphp

<div @class( $classes )>

	@if ( ! empty( $title ) )
		<p class="season-highlights__item-title">
			<x-escape :content="$title" />
		</p>
	@endif

	{!! $slot !!}
</div>
