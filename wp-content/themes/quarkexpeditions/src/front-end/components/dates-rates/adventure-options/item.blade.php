@props( [
	'name'    => '',
	'icon'    => 0,
	'is_paid' => false,
] )

@php
	if ( empty( $icon ) || empty( $name ) ) {
		return;
	}

	// Classes.
	$classes = [ 'dates-rates__adventure-options-item' ];
2
	if ( true === $is_paid ) {
		$classes[] = 'dates-rates__adventure-options-item--paid';
	}

	$icon_image_args = [
		'size' =>       [
			'width'   => 56,
			'height'  => 56,
		],
	];
@endphp

<li @class( $classes )>
	<div class="dates-rates__adventure-options-item-icon">
		<x-image :image_id="$icon" :args="$icon_image_args" />
	</div>

	<div class="dates-rates__adventure-options-item-content-wrap">
		<div class="dates-rates__adventure-options-item-name">
			<x-escape :content="$name" />
		</div>

		<div class="dates-rates__adventure-options-item-content">
			{!! $slot !!}
		</div>
	</div>
</li>
