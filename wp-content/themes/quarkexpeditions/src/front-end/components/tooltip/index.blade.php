@props( [
	'icon' => 'info',
] )

@php
	if ( empty( $icon ) || empty( $slot ) ) {
		return;
	}

	$popover_uid = quark_generate_unique_dom_id();

	wp_enqueue_script( 'popover-polyfill' );
@endphp

<quark-tooltip class="tooltip">
	<button popovertarget="{{ $popover_uid }}" class="tooltip__icon">
		<x-svg name="{{ $icon }}" />
	</button>

	<div class="tooltip__description" id="{{ $popover_uid }}" popover>
		<div class="tooltip__description-content">
			{!! $slot !!}
		</div>
		<span class="tooltip__arrow"></span>
	</div>

</quark-tooltip>
