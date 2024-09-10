@props( [
	'name'  => '',
	'label' => '',
	'value' => '',
	'count' => '0',
] )

@php
	$label = $label . " ({$count})";
@endphp

<x-form.checkbox :name="$name1" :label="$label" :value="$value" data-label="{{ $label }}" />
