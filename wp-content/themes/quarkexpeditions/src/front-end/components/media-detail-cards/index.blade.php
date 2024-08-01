@props ( [
	'condensed' => false,
	'title'     => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'media-detail-cards' ];

	if ( $condensed ) {
		$classes[] = 'media-detail-cards--condensed';
	}

@endphp

<x-section @class( $classes )>
	@if ( ! empty( $title ) )
		<x-section.heading>
			<x-section.title :title="$title" align="left" heading_level="4" />
		</x-section.heading>
	@endif
	{!! $slot !!}
</x-section>
