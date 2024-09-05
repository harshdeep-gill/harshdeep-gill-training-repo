@props( [
	'months' => [],
] )

@php
	if ( empty( $months ) || ! is_array( $months ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-departure-months>
	<x-form.field-group>
		@foreach ( $months as $filter_value => $filter_label )
			<x-dates-rates.filters.checkbox name="months" :label="$filter_label" :value="$filter_value" data-label="{!! $filter_label !!}" />
		@endforeach
	</x-form.field-group>
</quark-dates-rates-filter-departure-months>
