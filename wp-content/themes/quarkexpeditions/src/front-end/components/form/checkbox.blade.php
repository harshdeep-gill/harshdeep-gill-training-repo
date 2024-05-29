@props( [
	'id'    => '',
	'label' => '',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );
@endphp

<div class="checkbox-container">
	<input
		type="checkbox"
		{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}
	>

	<x-form.label :id="$id">
		@if ( ! empty( $label ) )
			<x-escape :content="$label"/>
		@endif
	</x-form.label>
</div>
