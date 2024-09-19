@props( [
	'modal_id' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-modal.modal-open :modal_id="$modal_id">
	<x-content :content="$slot" />
</x-modal.modal-open>
