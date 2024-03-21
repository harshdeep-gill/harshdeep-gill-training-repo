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

	// Appropriate modal components for each form.
	$form_modal_component_mapping = [
		'inquiry-form'         => 'inquiry-form-modal',
		'inquiry-form-compact' => 'inquiry-form-modal',
	];

	// Check if $form_id is valid.
	if ( ! array_key_exists( $form_id, $form_modal_component_mapping ) ) {
		// Invalid, bail.
		return;
	}

	// Get the modal component name for the $form_id
	$modal_component = $form_modal_component_mapping[ $form_id ];

	// $modal_id will be different for each $form_id.
	$modal_id = $form_id . '-modal';
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
