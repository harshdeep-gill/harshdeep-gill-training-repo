@props( [
	'id'    => '',
	'label' => '',
	'class' => ''
] )

@php
	quark_enqueue_style( 'intl-tel-input-css' );
	quark_enqueue_script( 'intl-tel-input-js' );

	$id = quark_get_form_field_id( $id ?? '' );
@endphp

@if ( ! empty( $label ) )
	<label class="form-field__phone-label" for="{{ $id }}">
		<x-escape :content="$label"/>
	</label>
@endif

<div class="form-field__phone">
	<input
		autocomplete="nop"
		type="tel"
		@class([ 'form__phone', 'form-field', $class ])
		{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' && $key !== 'class' )->merge( [ 'id' => $id ] ) }}
	>
	<div class="form__phone_validation_error form-field__error"></div>
</div>
