@props( [
	'id'    => '',
	'label' => '',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );
@endphp

@if ( ! empty( $label ) )
	<x-form.label :id="$id">
		<x-escape :content="$label"/>
	</x-form.label>
@endif

<input {{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}>
