@props( [
	'name'            => '',
	'path'            => '',
	'title'           => '',
	'media'           => true,
	'fullscreen_icon' => 'hidden',
] )

@php
	if ( empty( $name ) || empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'glightbox' );
	quark_enqueue_script( 'glightbox' );

	$classes = [ 'media-lightbox' ];

	if ( 'visible' === $fullscreen_icon ) {
		$classes[] = 'media-lightbox--fullscreen-icon-visible';
	}
@endphp

<quark-media-lightbox
	@class( $classes )
	name="{{ $name }}"
	>
	<a
		href="{{ $path }}"
		class="media-lightbox__link glightbox"
		data-gallery="{{ $name }}"
		data-zoomable="false"
		data-draggable="false"
	   	@if ( ! empty( $title ) )
		   data-glightbox="title: {{ $title }}"
		@endif
	>
		@if ( true === $media )
			<figure class="media-lightbox__image-wrap">
				{!! $slot !!}
			</figure>

			<span class="media-lightbox__fullscreen">
				<x-svg name="fullscreen" />
			</span>
		@else
			{!! $slot !!}
		@endif
	</a>
</quark-media-lightbox>
