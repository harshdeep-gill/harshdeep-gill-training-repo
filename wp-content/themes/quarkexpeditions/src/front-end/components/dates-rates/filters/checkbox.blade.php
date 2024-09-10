@props( [
	'name'  => '',
	'label' => '',
	'value' => '',
	'count' => '0',
] )

@php
	$label = $label . " ({$count})";
@endphp

<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $label }}" />
