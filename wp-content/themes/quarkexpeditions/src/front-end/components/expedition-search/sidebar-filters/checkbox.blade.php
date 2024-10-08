@props( [
	'name'      => '',
	'label'     => '',
	'value'     => '',
	'count'     => '0',
	'parent'    => 0,
	'image_url' => '',
] )

@php
	$data_label = $label;

	if ( ! empty( $count ) ) {
		$label = $label . " ({$count})";
	}
@endphp

@if ( empty( $parent ) )
	<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $data_label }}" data-image-url="{!! esc_url( $image_url ) !!}" />
@else
	<x-form.checkbox :name="$name" :label="$label" :value="$value" data-label="{{ $data_label }}" data-parent="{{ $parent }}" data-image-url="{!! esc_url( $image_url ) !!}" />
@endif
