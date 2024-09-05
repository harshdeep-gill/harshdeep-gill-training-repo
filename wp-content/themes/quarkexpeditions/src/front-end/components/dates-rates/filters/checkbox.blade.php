@props( [
	'name'  => '',
	'label' => '',
	'value' => '',
] )

<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{!! $label !!}" />
