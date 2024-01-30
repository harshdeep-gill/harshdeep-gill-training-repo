@props( [
	'modal_id' => '',
] )

@php
	if ( empty( $slot ) || empty( $modal_id ) ) {
		return;
	}
@endphp

<x-form>
	{!! $slot !!}
	<x-form.buttons>
		<x-modal.open-modal modal_id="{{ $modal_id }}">
			<x-button type="button">
				Request a Quote
				<x-button.sub-title title="It only takes 2 minutes!" />
			</x-button>
		</x-modal.open-modal>
	</x-form.buttons>
</x-form>
