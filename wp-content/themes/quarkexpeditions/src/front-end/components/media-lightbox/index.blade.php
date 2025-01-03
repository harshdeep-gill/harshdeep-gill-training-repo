@props( [
	'name'                     => '',
	'path'                     => '',
	'title'                    => '',
	'media'                    => true,
	'fullscreen_icon'          => 'hidden',
	'fullscreen_icon_position' => 'bottom',
	'image_id'                 => 0,
] )

@php
	if ( empty( $name ) || empty( $slot ) ) {
		return;
	}

	quark_enqueue_style( 'tp-lightbox' );
	quark_enqueue_script( 'tp-lightbox' );

	$classes = [ 'media-lightbox' ];
	$fullscreen_classes = [ 'media-lightbox__fullscreen' ];

	if ( 'visible' === $fullscreen_icon ) {
		$fullscreen_classes[] = 'media-lightbox__fullscreen-icon--visible';
	}

	// Add fullscreen icon position class.
	if ( ! empty( $fullscreen_icon_position ) ) {
		$fullscreen_icon_positions = [ 'bottom', 'top' ];

		if ( in_array( $fullscreen_icon_position, $fullscreen_icon_positions, true ) ) {
			$fullscreen_classes[] = sprintf( 'media-lightbox__fullscreen-icon--position-%s', $fullscreen_icon_position );
		}
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
				<span @class($fullscreen_classes)>
					<x-svg name="fullscreen" />
				</span>
			@else
				{!! $slot !!}
			@endif
		</button>
		<template>
			@if ( ! empty( $path ) )
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
				@elseif ( str_contains( $path, 'wistia.com' ) )
					<x-wistia-embed :url="$path" />
				@endif

			@elseif ( ! empty( $image_id ) )
				<x-image
					:image_id="$image_id"
					:args="[
						'size' => [
							'width'  => 1200,
							'height' => 600,
						],
						'responsive' => [
							'sizes'  => [ '(min-width: 1140px) 1200px', '100vw' ],
							'widths' => [ 360, 400, 600, 800, 1024, 1200 ],
						],
						'focal_point' => [],
						'transform' => [
							'crop' => 'fit',
						],
					]"
				/>
			@endif
			<p class="media-lightbox__caption">
				<x-escape :content="$title" />
			</p>
		</template>
	</tp-lightbox-trigger>

	<x-once id="media-lightbox">
		<tp-lightbox id="media-lightbox" class="media-lightbox" swipe="yes">
			<dialog class="media-lightbox__dialog">
				<tp-lightbox-close class="media-lightbox__close">
					<button><x-svg name="cross" /></button>
				</tp-lightbox-close>

				<tp-lightbox-count class="media-lightbox__count" format="$current/$total"></tp-lightbox-count>

				<tp-lightbox-content class="media-lightbox__content"></tp-lightbox-content>

				<tp-lightbox-previous class="media-lightbox__prev">
					<button class="media-lightbox__prev-button"><x-svg name="chevron-left" /></button>
				</tp-lightbox-previous>

				<tp-lightbox-next class="media-lightbox__next">
					<button class="media-lightbox__next-button"><x-svg name="chevron-left" /></button>
				</tp-lightbox-next>
			</dialog>
		</tp-lightbox>
	</x-once>
</quark-media-lightbox>
