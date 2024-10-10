@props ( [
	'name'  => '',
	'value' => 0,
	'id'    => ''
] )

@aware ( [
	'min' => 0,
	'max' => 0,
] )

@php
	if ( empty( $name ) || empty( $id ) ) {
		return;
	}
@endphp

<input
	type="range"
	name="{{ $name }}"
	min="{{ $min }}"
	max="{{ $max }}"
	value="{{ $value }}"
	class="form__range-slider__input"
	aria-labelledby={{ $id }} {{-- We are using this because we have two inputs and one label.  --}}
	id="{{ $id }}"
/>
