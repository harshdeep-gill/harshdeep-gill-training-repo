@props( [
	'id'       => '',
	'label'    => '',
	'optional' => false,
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );
@endphp

@if ( ! empty( $label ) )
	<x-form.label :id="$id">
		<x-escape :content="$label"/>
		@if ( $optional )
			<span class="form__label-optional-text">{{ __( '(optional)', 'qrk' ) }}</span>
		@endif
	</x-form.label>
@endif

<textarea {{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}>{{ $slot }}</textarea>
