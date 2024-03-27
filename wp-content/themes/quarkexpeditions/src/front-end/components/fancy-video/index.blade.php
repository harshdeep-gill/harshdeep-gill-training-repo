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
			'width'   => 1200,
			'height'  => 720,
			'picture' => [
				'(min-width: 1400px)' => [ 1600, 900 ],
				'(min-width: 1280px)' => [ 1400, 789 ],
				'(min-width: 1024px)' => [ 1200, 800 ],
				'(min-width: 768px)'  => [ 900, 600 ],
				'(min-width: 500px)'  => [ 700, 467 ],
				'(min-width: 375px)'  => [ 500, 334 ],
				'(min-width: 320px)'  => 400,
			],
		],
		'transform' => [
			'crop'    => 'lfill',
			'quality' => 90,
		]
	];
@endphp

<quark-fancy-video class="fancy-video typography-spacing" url="{{ $url }}">
	<div class="fancy-video__wrapper">
		<div class="fancy-video__cover">
			<x-image
				class="fancy-video__image"
				:image_id="$image_id"
				:args="$image_args"
			/>

			@if ( ! empty( $title ) )
				<div class="fancy-video__content">
					<p class="h2" class="fancy-video__title">
						<x-escape :content="$title" />
					</p>
				</div>
			@endif

			<div class="fancy-video__play-btn-wrapper">
				<button class="fancy-video__play-btn">
					<x-svg name="play"/>
				</button>
			</div>
		</div>
	</div>

	@if ( ! empty( $url ) )
		<iframe class="fancy-video__iframe" width="1200" height="620" src="{{ $url }}" title="{{ $title }}" allow="modestbranding; accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
	@endif
</quark-fancy-video>
