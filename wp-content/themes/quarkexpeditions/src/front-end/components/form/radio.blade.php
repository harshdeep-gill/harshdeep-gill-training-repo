@props( [
	'id'    => '',
	'label' => '',
	'class' => '',
	'checked' => '',
] )

@php
	$id = quark_get_form_field_id( $id ?? '' );

	$classes = [ 'radio-container' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<div @class( $classes )>
	<input
		type="radio"
		{{ $attributes->filter( fn ( $value, $key ) => $key !== 'label' )->merge( [ 'id' => $id ] ) }}
		{{ $checked ? 'checked' : '' }}
	>

	<x-form.label :id="$id">
		@if ( ! empty( $label ) )
			<x-escape :content="$label"/>
		@elseif ( ! empty( $slot ) )
			<x-content :content="$slot" />
		@endif
	</x-form.label>
</div>
