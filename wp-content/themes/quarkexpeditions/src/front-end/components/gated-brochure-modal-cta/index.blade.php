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

@php
	if ( empty( $slot ) || empty( $form_id ) ) {
		return;
	}

	$classes = [ 'gated-brochure-modal-cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	// Get the modal component name for the $form_id
	$modal_component = 'gated-brochure-modal';

	/**
	 * $modal_id will be different for each $brochure_id and $thank_you_page url.
	 */
	$modal_id = quark_generate_dom_id( $brochure_id . $thank_you_page );
@endphp

<quark-gated-brochure-modal-cta @class( $classes )>
	<x-modal.modal-open :modal_id="$modal_id">
		<x-content :content="$slot" />
	</x-modal.modal-open>

	<x-once id="{{ $modal_id }}">
		{!!
			quark_get_component(
				$modal_component,
				[
					'thank_you_page' => $thank_you_page,
					'form_id'        => $form_id,
					'modal_id'       => $modal_id,
					'modal_title'    => $modal_title,
					'countries'      => $countries,
					'states'         => $states,
					'brochure_id'    => $brochure_id,
					'brochure_url'   => $brochure_url,
				]
			)
		!!}
	</x-once>
</quark-gated-brochure-modal-cta>
