@props( [
	'class'      => '',
	'validation' => [],
] )

@aware( [
	'id'         => '',
	'name'       => '',
	'validation' => [],
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$for = $name ?? '';
	if ( ! empty( $id ) ) {
		$for = $id;
	}
@endphp

<label
	@if ( ! empty( $for ) )
		for="{{ $for }}"
	@endif

	@if ( ! empty( $class ) )
		class="{{ $class }}"
	@endif
>
	{!! $slot !!}
	<span class="form__required-indicator">*</span>
</label>
