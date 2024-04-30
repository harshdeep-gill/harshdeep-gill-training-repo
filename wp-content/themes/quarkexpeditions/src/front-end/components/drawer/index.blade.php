@props( [
	'id'    => '',
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'drawer', $class ];

	wp_enqueue_script( 'tp-modal' );
	wp_enqueue_style( 'tp-modal' );
@endphp

<tp-modal
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	overlay-click-close="yes"
>
	<tp-modal-content class="drawer__content">
		{!! $slot !!}
	</tp-modal-content>
</tp-modal>
