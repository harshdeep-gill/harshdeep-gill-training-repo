@props( [
	'id'    => '',
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'modal', $class ];

@endphp

<tp-modal
	@if ( ! empty( $id ) )
		id="{{ $id }}"
	@endif
	@class( $classes )
	overlay-click-close="yes"
>
	<tp-modal-content class="modal__content">
		<x-modal.close />
		{!! $slot !!}
	</tp-modal-content>
</tp-modal>
