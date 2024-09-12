@props( [
	'title'      => '',
	'validation' => [],
	'class'      => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'form-field-group' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}
@endphp

<x-form.field @class( $classes ) :validation="$validation">
	@if ( ! empty( $title ) )
		<x-form.label class="form-field-group__title">
			<x-escape :content="$title"/>
		</x-form.label>
	@endif
	<div class="form-field-group__group">
		{!! $slot !!}
	</div>
</x-form.field>
