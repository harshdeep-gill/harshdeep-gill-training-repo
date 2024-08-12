@props( [
	'title' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$dom_id = quark_generate_unique_dom_id();
@endphp

<li>
	<x-modal.modal-open modal_id="offers-modal-{{ $dom_id }}">
		<x-button class="departure-cards__offer-count-button">
			+ <span class="departure-cards__offer-count"></span>
		</x-button>
	</x-modal.modal-open>

	<x-modal id="offers-modal-{{ $dom_id }}" class="departure-cards__offer-modal">
		@if ( ! empty( $title ) )
			<x-modal.header>
				<h4 class="departure-cards__modal-title"><x-escape :content="$title" /></h4>
			</x-modal.header>
		@endif

		<x-modal.body>
			{!! $slot !!}
		</x-modal.body>
	</x-modal>
</li>
