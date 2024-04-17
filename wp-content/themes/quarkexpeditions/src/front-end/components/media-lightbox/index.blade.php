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

<quark-media-lightbox>
	<tp-lightbox-trigger
		class="media-lightbox__link"
		lightbox="media-lightbox"
		group="{{ $name }}"
	>
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
			@if ( str_contains( $path, 'youtube.com' ) )
				<iframe
					data-path="{{ $path }}"
					src="{{ $path }}"
					title="YouTube video player"
					frameborder="0"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					referrerpolicy="strict-origin-when-cross-origin"
					allowfullscreen
				></iframe>
			@else
				<img src="{{ $path }}"/>
			@endif
		</template>
	</tp-lightbox-trigger>
</quark-media-lightbox>

<x-once id="media-lightbox">
	<tp-lightbox id="media-lightbox" class="media-lightbox" close-on-overlay-click="yes">
		<dialog class="media-lightbox__dialog">
			<tp-lightbox-close class="media-lightbox__close">
				<button><x-svg name="cross" /></button>
			</tp-lightbox-close>

			<tp-lightbox-content class="media-lightbox__content"></tp-lightbox-content>

			<tp-lightbox-previous class="media-lightbox__prev">
				<button><x-svg name="chevron-left" /></button>
			</tp-lightbox-previous>

			<tp-lightbox-next class="media-lightbox__next">
				<button><x-svg name="chevron-left" /></button>
			</tp-lightbox-next>

			<tp-lightbox-count class="media-lightbox__count" format="$current of $total"></tp-lightbox-count>
		</dialog>
	</tp-lightbox>
</x-once>
