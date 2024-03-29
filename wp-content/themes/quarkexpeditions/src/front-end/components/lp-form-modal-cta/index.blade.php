@props( [
	'class'          => '',
	'form_id'        => '',
	'thank_you_page' => '',
	'hidden_fields'  => [],
] )

@php
	if ( empty( $slot ) || empty( $form_id ) ) {
		return;
	}

	$classes = [ 'lp-form-modal-cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$show_hidden_fields = false;

	if ( ! empty( $hidden_fields ) ) {
		$show_hidden_fields = true;
	}

	// Get the modal component name for the $form_id
	$modal_component = 'inquiry-form-modal';

	// $modal_id will be different for each $form_id and $thank_you_page url.
	$modal_id = substr( md5( $form_id . $thank_you_page ), 0, 10 );
@endphp

<quark-lp-form-modal-cta
	@class( $classes )
	data-polar-region="{{ $hidden_fields['polar_region'] ?? '' }}"
	data-season="{{ $hidden_fields['season'] ?? '' }}"
	data-ship="{{ $hidden_fields['ship'] ?? '' }}"
	data-sub-region="{{ $hidden_fields['sub_region'] ?? '' }}"
	data-expedition="{{ $hidden_fields['expedition'] ?? '' }}"
	data-modal-id="{{ $modal_id }}"
>
	<x-modal.modal-open :modal_id="$modal_id">
		<x-content :content="$slot" />
	</x-modal.modal-open>

	<x-once id="{{ $modal_id }}">
		{!!
			quark_get_component(
				$modal_component,
				[
					'thank_you_page'     => $thank_you_page,
					'form_id'            => $form_id,
					'modal_id'           => $modal_id,
					'show_hidden_fields' => $show_hidden_fields,
				]
			)
		!!}
	</x-once>

</quark-lp-form-modal-cta>

