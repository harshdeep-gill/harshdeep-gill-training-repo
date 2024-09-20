@props( [
	'modal_id'    => '',
	'modal_title' => '',
] )

<x-once id="{{ $modal_id }}">
	<x-modal
		class="gated-brochure-modal"
		id="{{ $modal_id }}"
	>
		<div class="gated-brochure-modal__modal-content">
			@if ( ! empty( $modal_title ) )
				<x-modal.header>
					<h3>{{ $modal_title }}</h3>
				</x-modal.header>
			@endif

			<x-modal.body>
				{!! $slot !!}
			</x-modal.body>
		</div>
	</x-modal>
</x-once>
