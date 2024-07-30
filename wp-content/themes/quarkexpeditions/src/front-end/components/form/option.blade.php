@props( [
	'value'    => '',
	'label'    => '',
	'selected' => '',
] )

<tp-multi-select-option
	value="{{ $value }}"
	label="{{ $label }}"
	selected="{{ $selected }}"
>
	{!! $slot !!}
</tp-multi-select-option>
