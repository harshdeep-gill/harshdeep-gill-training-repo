@props( [
	'modal_id'    => '',
	'modal_title' => '',
] )

<x-modal
	class="gated-brochure-modal__modal"
	id="{{ $modal_id }}"
>
	<quark-gated-brochure-modal class="gated-brochure-modal__modal-content">
		@if ( ! empty( $modal_title ) )
			<x-modal.header>
				<h3>{{ $modal_title }}</h3>
			</x-modal.header>
		@endif

		<x-modal.body>
			{!! $slot !!}
		</x-modal.body>
	</quark-gated-brochure-modal>
</x-modal>
