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
	<span class="form__required-indicator">*</span>
</label>
