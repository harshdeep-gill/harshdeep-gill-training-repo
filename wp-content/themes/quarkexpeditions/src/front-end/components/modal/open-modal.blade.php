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

<quark-open-modal @class( [ $class, 'modal__open-modal' ] ) modal-id="{{ $modal_id }}">
	{!! $slot !!}
</quark-open-modal>
