@props( [
	'value'    => '',
	'label'    => '',
	'selected' => '',
] )

<tp-multi-select-option
	value="{!! esc_attr( $value ) !!}"
	label="{{ $label }}"
	@if ( ! empty( $selected ) )
		selected="{{ $selected }}"
	@endif
>
	{!! $slot !!}
</tp-multi-select-option>
