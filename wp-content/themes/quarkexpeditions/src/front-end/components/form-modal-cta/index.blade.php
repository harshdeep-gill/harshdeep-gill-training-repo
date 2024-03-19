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

	switch ( $form_id ) {
		case 'inquiry-form':

		case 'inquiry-form-compact':
			$modal_id = 'inquiry-form-modal';
			break;

		default:
			return;
	}
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
