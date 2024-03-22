@props( [
	'class'          => '',
	'form_id'        => '',
	'thank_you_page' => '',
] )

@php
	if ( empty( $slot ) || empty( $form_id ) ) {
		return;
	}

	$classes = [ 'form-modal-cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	// Get the modal component name for the $form_id
	$modal_component = 'inquiry-form-modal';

	// $modal_id will be different for each $form_id.
	$modal_id = $form_id . quark_url_to_css_id( $thank_you_page ) . '-modal';
@endphp

<x-modal.modal-open @class( $classes ) modal_id="{{ $modal_id }}">
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
			]
		)
	!!}
</x-once>
