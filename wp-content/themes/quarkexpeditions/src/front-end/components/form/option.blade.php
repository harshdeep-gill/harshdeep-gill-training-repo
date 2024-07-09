@props( [
	'value' => '',
	'label' => '',
] )

<tp-multi-select-option
	value="{{ $value }}"
	label="{{ $label }}"
>
	{!! $slot !!}
</tp-multi-select-option>
