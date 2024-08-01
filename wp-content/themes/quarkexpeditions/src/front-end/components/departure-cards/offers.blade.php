@props( [
	'title'       => '',
	'modal_title' => '',
	'items'       => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}

	$dom_id = quark_generate_unique_dom_id();
@endphp

<div class="departure-cards__offers">
	@if ( ! empty( $title ) )
		<p class="departure-cards__offers-title"><x-escape :content="$title" /></p>
	@endif

	<ul class="departure-cards__offers-list">
		@foreach ( $items as $item )
			<li class="departure-cards__offer">
				<x-escape :content="$item" />
			</li>
		@endforeach

		<li>
			<x-modal.modal-open modal_id="offers-modal-{{ $dom_id }}">
				<x-button class="departure-cards__offer-count-button">
					+ <span class="departure-cards__offer-count">2</span>
				</x-button>
			</x-modal.modal-open>
		</li>
	</ul>
</div>

<x-modal id="offers-modal-{{ $dom_id }}">
	@if ( ! empty( $modal_title ) )
		<x-modal.header>
			<h4 class="departure-cards__modal-title"><x-escape :content="$title" /></h4>
		</x-modal.header>
	@endif

	<x-modal.body>
		<ul class="departure-cards__modal-offers-list">
			@foreach ( $items as $item )
				<li class="departure-cards__modal-offer">
					<x-escape :content="$item" />
				</li>
			@endforeach
		</ul>
	</x-modal.body>
</x-modal>
