@props( [
	'expeditions' => [],
] )

@php
	if ( empty( $expeditions ) || ! is_array( $expeditions ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-expeditions>
	<x-form.field-group>
		@foreach ( $expeditions as $filter_value => $filter_label )
			<x-dates-rates.filters.checkbox name="expeditions" :label="$filter_label" :value="$filter_value" data-label="{{ $filter_label }}" />
		@endforeach
	</x-form.field-group>
</quark-dates-rates-filter-expeditions>
