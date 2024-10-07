@props( [
	'modal_id' => '',
] )

@php
	if ( empty( $modal_id ) ) {
		return;
	}
@endphp

<x-modal.modal-open class="product-options-cards__modal-cta" :modal_id="$modal_id">
	<x-content :content="$slot" />
</x-modal.modal-open>
