@props( [
	'id'    => '',
	'label' => '',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );
@endphp

@if ( ! empty( $label ) )
	<label class="label typography-spacing" for="{{ $id }}">
		<x-escape :content="$label"/>
	</label>
@endif

<input
	type="tel"
	{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}
>
