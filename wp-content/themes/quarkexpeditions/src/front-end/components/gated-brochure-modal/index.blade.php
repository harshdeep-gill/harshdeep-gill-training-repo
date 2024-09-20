@props( [
	'modal_id'       => '',
	'modal_title'    => '',
	'image_id'       => '',
	'thank_you_page' => '',
	'form_id'        => 'download-gated-brochure',
	'countries'      => [],
	'states'         => [],
	'brochure_id'    => '',
	'brochure_url'   => '',
] )

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
			<x-gated-brochure-modal.left>
				<x-gated-brochure-modal.image image_id="36" />
			</x-gated-brochure-modal.left>

			<x-gated-brochure-modal.right>
				<x-gated-brochure-modal.form modal_title="{{ $modal_title }}" :countries="$countries" :states="$states" brochure_url="#" />
			</x-gated-brochure-modal.right>
		</x-modal.body>
	</div>
</x-modal>
