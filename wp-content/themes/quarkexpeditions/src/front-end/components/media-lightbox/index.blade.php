@props( [
	'name'  => '',
	'path'  => '',
	'title' => '',
	'media' => true,
] )

@php
	if ( empty( $name ) || empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'glightbox' );
	quark_enqueue_script( 'glightbox' );
@endphp

<quark-media-lightbox class="media-lightbox" name="{{ $name }}">
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
		@else
			{!! $slot !!}
		@endif
	</a>
</quark-media-lightbox>
