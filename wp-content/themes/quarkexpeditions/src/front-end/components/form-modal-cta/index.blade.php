@props( [
	'class'          => '',
	'form_id'        => '',
	'thank_you_page' => '',
] )

@php
	if ( empty( $slot ) || empty( $form_id ) ) {
		return;
	}

	$modal_id = $form_id . '-modal';

	$classes = [ 'form-modal-cta' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-modal.modal-open @class( $classes ) :modal_id="$modal_id">
	<x-content :content="$slot" />
</x-modal.modal-open>

@switch( $form_id )
	@case( 'inquiry-form' )
		<x-once :id="$modal_id">
			<x-inquiry-form.modal thank_you_page="{{ $thank_you_page }}" />
		</x-once>
		@break
@endswitch
