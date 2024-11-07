@props( [
	'modal_id' => '',
] )

@php
	if ( empty( $slot ) || empty( $modal_id ) ) {
		return;
	}
@endphp

<x-modal.modal-open :modal_id="$modal_id" class="itinerary-details__modal-nav-link">
	{!! $slot !!}
</x-modal.modal-open>
