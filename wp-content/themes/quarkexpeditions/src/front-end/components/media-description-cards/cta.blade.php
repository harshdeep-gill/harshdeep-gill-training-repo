@props( [
	'url'        => '',
	'text'       => '',
	'new_window' => false,
	'class'      => '',
	'color'      => 'black',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'media-description-cards__cta-button' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<x-button
		href="{{ $url }}"
		:target="! empty( $new_window ) ? '_blank' : ''"
		:color="$color"
		size="big"
		appearance="outline"
	>
		<x-escape :content="$text" />
	</x-button>
</div>
