@props( [
	'id'              => '',
	'label'           => '',
	'unchecked_value' => '',
	'value'           => 'true',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );
@endphp

<div class="checkbox-container">
	@if ( ! empty( $unchecked_value ) )
		<input
			type="hidden"
			{{ $attributes->filter( fn ( $value, $key ) => 'label' !== $key )->merge( [ 'value' => esc_attr( $unchecked_value ) ] ) }}
		>
	@endif
	<input
		type="checkbox"
		{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id, 'value' => $value ] ) }}
	>

	<x-form.label :id="$id">
		@if ( ! empty( $label ) )
			<x-escape :content="$label"/>
		@endif
	</x-form.label>
</div>
