@props( [
	'name'   => '',
	'label'  => '',
	'value'  => '',
	'count'  => '0',
	'parent' => 0,
] )

@php
	$data_label = $label;

	if ( ! empty( $count ) ) {
		$label = $label . " ({$count})";
	}
@endphp

@if ( empty( $parent ) )
	<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $data_label }}" />
@else
	<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $data_label }}" data-parent="{{ $parent }}" />
@endif
