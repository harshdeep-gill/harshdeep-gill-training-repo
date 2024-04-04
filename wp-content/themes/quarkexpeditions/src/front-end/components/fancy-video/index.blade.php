@props( [
	'title'    => '',
	'url'      => '',
	'image_id' => '',
] )

@php
	if ( empty( $image_id ) || empty( $url ) ) {
		return;
	}

	$image_args = [
		'size' => [
			'width'  => 544,
			'height' => 306,
		],
		'responsive' => [
			'sizes'  => [ '(min-width: 720px) 544px', '100vw' ],
			'widths' => [ 375, 450, 500, 576, 640, 980, 1024, 1200 ],
		],
	];
@endphp

<quark-fancy-video class="fancy-video typography-spacing" url="{{ $url }}">
	<div class="fancy-video__wrapper">
		@if ( ! empty( $title ) )
			<div class="fancy-video__content">
				<h3 class="fancy-video__title h4">
					<x-escape :content="$title" />
				</h3>
			</div>
		@endif

		<div class="fancy-video__cover-wrap">
			<div class="fancy-video__cover">
				<x-image
					class="fancy-video__image"
					:image_id="$image_id"
					:args="$image_args"
				/>

				<div class="fancy-video__play-btn-wrapper">
					<button class="fancy-video__play-btn">
						<x-svg name="play"/>
					</button>
				</div>
			</div>

			@if ( ! empty( $url ) )
				<iframe class="fancy-video__iframe" width="1200" height="620" src="{{ $url }}" title="{{ $title }}" allow="modestbranding; accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
			@endif
		</div>
	</div>
</quark-fancy-video>
