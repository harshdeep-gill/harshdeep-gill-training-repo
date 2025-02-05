@props( [
	'title' => '',
	'icon'  => 0,
] )

@php
	if ( empty( $title ) ) {
		return;
	}

	$icon_image_args = [
		'size' =>       [
			'height' => 40,
			'width'  => 'auto',
		],
	];
@endphp

<li class="departure-cards__option">
	@if ( ! empty( $icon ) )
		<div class="departure-cards__option-icon">
			<x-image :image_id="$icon" :args="$icon_image_args" />
		</div>
	@endif
	<x-escape :content="$title" />
</li>
