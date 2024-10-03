@props( [
	'id'      => '',
	'label'   => '',
	'class'   => '',
	'checked' => '',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );

	$classes = [ 'radio-container' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	// Filter data attributes.
	$data_attributes = $attributes->filter( fn ( $value, $key ) => str_starts_with( $key, 'data-' ) );

	// Without data attributes.
	$attributes = $attributes->filter( fn ( $value, $key ) => ! str_starts_with( $key, 'data-' ) );

@endphp

<div @class( $classes )>
	<input
		type="radio"
		{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}
		{{ $checked ? 'checked' : '' }}
		{!! $data_attributes !!}
	>

	<x-form.label :id="$id">
		@if ( ! empty( $label ) )
			<x-escape :content="$label"/>
		@elseif ( ! empty( $slot ) )
			<x-content :content="$slot" />
		@endif
	</x-form.label>
</div>
