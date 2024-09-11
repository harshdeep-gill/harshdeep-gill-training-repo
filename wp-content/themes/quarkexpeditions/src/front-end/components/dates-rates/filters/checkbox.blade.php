@props( [
	'name'  => '',
	'label' => '',
	'value' => '',
	'count' => '0',
] )

@php
	$data_label = $label;
	$label = $label . " ({$count})";
@endphp

<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $data_label }}" />
