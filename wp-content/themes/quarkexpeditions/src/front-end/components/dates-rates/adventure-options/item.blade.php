@props( [
	'name'    => '',
	'icon'    => '',
	'is_paid' => false,
] )

@php
	if ( empty( $icon ) || empty( $name ) ) {
		return;
	}

	// Classes.
	$classes = [ 'dates-rates__adventure-options-item' ];

	if ( true === $is_paid ) {
		$classes[] = 'dates-rates__adventure-options-item--paid';
	}
@endphp

<li @class( $classes )>
	<div class="dates-rates__adventure-options-item-icon">
		<x-svg name="{{ $icon }}" />
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
