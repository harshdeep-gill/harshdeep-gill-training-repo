@props( [
	'name'   => '',
	'label'  => '',
	'value'  => '',
	'count'  => '0',
	'parent' => 0,
] )

@if ( empty( $parent ) )
	<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $label }}" />
@else
	<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $label }}" data-parent="{{ $parent }}" />
@endif
