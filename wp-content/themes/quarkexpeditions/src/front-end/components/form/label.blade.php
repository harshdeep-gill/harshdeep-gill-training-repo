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
>
	{!! $slot !!}
	@if ( in_array( 'required', $validation, true ) )
		<span class="form__required-indicator">*</span>
	@endif
</label>
