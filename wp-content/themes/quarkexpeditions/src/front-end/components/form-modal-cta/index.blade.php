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

	$form_modal_mapping = [
		'inquiry-form'         => 'inquiry-form-modal',
		'inquiry-form-compact' => 'inquiry-form-modal',
	];

	if ( ! array_key_exists( $form_id, $form_modal_mapping ) ) {
		return;
	}

	$modal_id = $form_modal_mapping[ $form_id ];
@endphp

<x-modal.modal-open @class( $classes ) modal_id="{{ $modal_id }}">
	<x-content :content="$slot" />
</x-modal.modal-open>

<x-once id="{{ $modal_id }}">
	@switch ( $form_id )
		@case ( 'inquiry-form' )
		@case ( 'inquiry-form-compact' )
			<x-form-modals.inquiry-form-modal thank_you_page="{{ $thank_you_page }}" />
			@break
		@endswitch
</x-once>
