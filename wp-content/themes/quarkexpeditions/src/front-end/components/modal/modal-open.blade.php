@props( [
	'modal_id' => '',
	'class'    => '',
] )

@php
	// This component should be wrapped in a button.
	if ( empty( $slot ) || empty( $modal_id ) ) {
		return;
	}
@endphp

<quark-modal-open @class( [ $class, 'modal__modal-open' ] ) modal-id="{{ $modal_id }}">
	{!! $slot !!}
</quark-modal-open>
