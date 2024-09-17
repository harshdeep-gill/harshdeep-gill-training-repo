@props( [
	'class'          => '',
	'form_id'        => '',
	'thank_you_page' => '',
	'countries'      => [],
	'states'         => [],
	'hidden_fields'  => [],
] )

@php
	if ( empty( $slot ) || empty( $form_id ) ) {
		return;
	}

	$classes = [ 'form-two-step__modal-cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	/**
	 * $modal_id will be different for each $form_id and $thank_you_page url.
	 */
	$modal_id = quark_generate_dom_id( $form_id . $thank_you_page );
@endphp

<quark-form-two-step-modal-cta
	@class( $classes )
	data-polar-region="{{ $hidden_fields['polar_region'] ?? '' }}"
	data-ship="{{ $hidden_fields['ship'] ?? '' }}"
	data-expedition="{{ $hidden_fields['expedition'] ?? '' }}"
	data-season="{{ $hidden_fields['season'] ?? '' }}"
	data-modal-id="{{ $modal_id }}"
>
	<x-modal.modal-open :modal_id="$modal_id">
		<x-content :content="$slot" />
	</x-modal.modal-open>

	<x-once id="{{ $modal_id }}">
		<x-form-two-step.modal
			:thank_you_page="$thank_you_page"
			:form_id="$form_id"
			:modal_id="$modal_id"
			:countries="$countries"
			:states="$states"
		/>
	</x-once>

</quark-form-two-step-modal-cta>

