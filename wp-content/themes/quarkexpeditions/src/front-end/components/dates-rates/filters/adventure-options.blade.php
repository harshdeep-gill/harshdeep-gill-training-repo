@props( [
	'adventure_options' => [],
] )

@php
	if ( empty( $adventure_options ) || ! is_array( $adventure_options ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-adventure-options>
	<x-form.field-group>
		@foreach ( $adventure_options as $filter_value => $filter_label )
			<x-dates-rates.filters.checkbox name="adventure_options" :label="$filter_label" :value="$filter_value" data-label="{{ $filter_label }}" />
		@endforeach
	</x-form.field-group>
</quark-dates-rates-filter-adventure-options>
