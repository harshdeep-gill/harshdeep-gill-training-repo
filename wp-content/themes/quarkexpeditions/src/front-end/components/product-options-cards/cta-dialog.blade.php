@props( [
	'dialog_id' => '',
] )

@php
	if ( empty( $dialog_id ) ) {
		return;
	}
@endphp

<x-dialog.dialog-open class="product-options-cards__dialog-cta" :dialog_id="$dialog_id">
	<x-content :content="$slot" />
</x-dialog.dialog-open>
