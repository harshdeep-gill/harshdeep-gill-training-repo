@props( [
	'name'  => '',
	'path'  => '',
	'title' => '',
	'media' => true,
] )

@php
	if ( empty( $name ) || empty( $slot ) || empty( $path ) ) {
		return;
	}

	quark_enqueue_style( 'tp-lightbox' );
	quark_enqueue_script( 'tp-lightbox' );
@endphp

<tp-lightbox-trigger class="media-lightbox__link" lightbox="media-lightbox" group="{{ $name }}">
	<button>
		@if ( true === $media )
			<figure class="media-lightbox__image-wrap">
				{!! $slot !!}
			</figure>
		@else
			{!! $slot !!}
		@endif
	</button>
	<template>
		@if ( str_starts_with( $path, 'https://youtube.com' ) )
			<iframe
				src="{{ $path }}"
				allow="modestbranding; accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
				allowfullscreen
			></iframe>
		@else
			<img src="{{ $path }}"/>
		@endif
	</template>
</tp-lightbox-trigger>

<x-once id="media-lightbox">
	<tp-lightbox id="media-lightbox" class="media-lightbox" close-on-overlay-click="yes">
		<dialog class="media-lightbox__dialog">
			<tp-lightbox-close class="media-lightbox__close">
				<button><x-svg name="cross" /></button>
			</tp-lightbox-close>

			<tp-lightbox-previous class="media-lightbox__prev">
				<button><x-svg name="chevron-left" /></button>
			</tp-lightbox-previous>

			<tp-lightbox-next class="media-lightbox__next">
				<button><x-svg name="chevron-left" /></button>
			</tp-lightbox-next>

			<tp-lightbox-content class="media-lightbox__content"></tp-lightbox-content>

			<tp-lightbox-count format="$current of $total"></tp-lightbox-count>
		</dialog>
	</tp-lightbox>
</x-once>
