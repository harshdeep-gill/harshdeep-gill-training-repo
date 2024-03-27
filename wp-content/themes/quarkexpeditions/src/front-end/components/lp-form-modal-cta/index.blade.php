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

	$modal_id = $form_id . '-modal';

	$classes = [ 'lp-form-modal-cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
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
	<x-modal.modal-open @class( $classes) :modal_id="$modal_id">
		<x-content :content="$slot" />
	</x-modal.modal-open>

	@switch( $form_id )
		@case( 'inquiry-form' )
			<x-once :id="$modal_id">
				<x-inquiry-form.modal thank_you_page="{{ $thank_you_page }}" />
			</x-once>
			@break
	@endswitch

</quark-lp-form-modal-cta>

