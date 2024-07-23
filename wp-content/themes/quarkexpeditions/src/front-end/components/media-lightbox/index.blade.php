@props( [
	'name'            => '',
	'path'            => '',
	'title'           => '',
	'media'           => true,
	'fullscreen_icon' => 'hidden',
] )

@php
	if ( empty( $name ) || empty( $slot ) || empty( $path ) ) {
		return;
	}

	quark_enqueue_style( 'tp-lightbox' );
	quark_enqueue_script( 'tp-lightbox' );

	$classes = [ 'media-lightbox' ];

	if ( 'visible' === $fullscreen_icon ) {
		$classes[] = 'media-lightbox--fullscreen-icon-visible';
	}
@endphp

<quark-media-lightbox class="media-lightbox__link">
	<tp-lightbox-trigger
		lightbox="media-lightbox"
		group="{{ $name }}"
	>
		<button>
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
		</button>
		<template>
			@if ( str_contains( $path, 'youtube.com' ) )
				<iframe
					data-path="{{ $path }}"
					src="{{ $path }}"
					title="{{ $title }}"
					frameborder="0"
					allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
					referrerpolicy="strict-origin-when-cross-origin"
					allowfullscreen
				></iframe>
			@else
				<img src="{{ $path }}"/>
			@endif
			<p class="media-lightbox__caption">{{ $title }}</p>
		</template>
	</tp-lightbox-trigger>
</quark-media-lightbox>

<x-once id="media-lightbox">
	<tp-lightbox id="media-lightbox" class="media-lightbox">
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
