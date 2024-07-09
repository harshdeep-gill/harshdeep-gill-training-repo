@props( [
	'title'      => '',
	'validation' => [],
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<x-form.field class="form-field-group" :validation="$validation">
	@if ( ! empty( $title ) )
		<x-form.label class="form-field-group__title">
			<x-escape :content="$title"/>
		</x-form.label>
	@endif
	<div class="form-field-group__group">
		{!! $slot !!}
	</div>
</x-form.field>
