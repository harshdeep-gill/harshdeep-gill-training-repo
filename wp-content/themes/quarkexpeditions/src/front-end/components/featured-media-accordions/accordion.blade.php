@props( [
	'class' => '',
	'title' => '',
	'id'    => '',
] )

@php
	$classes = [ 'featured-media-accordions__accordions' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-accordion.item :id="$id">
	<x-accordion.item-handle :title="$title" />
	<x-accordion.item-content>
		{!! $slot !!}
	</x-accordion.item-content>
</x-accordion.item>
