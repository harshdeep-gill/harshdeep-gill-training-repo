@props( [
	'image_id'   => 0,
	'appearance' => 'light',
	'content'    => '',
] )

@php
	if ( empty( $content ) ) {
		return;
	}
@endphp

<x-media-cta-banner appearance="{{ $appearance }}">
	<x-media-cta-banner.image image_id="{{ $image_id }}" />
	<x-media-cta-banner.content>
		{!! $content !!}
	</x-media-cta-banner.content>
</x-media-cta-banner>
