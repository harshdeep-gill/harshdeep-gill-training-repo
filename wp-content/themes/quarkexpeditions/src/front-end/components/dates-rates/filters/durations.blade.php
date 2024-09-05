@props( [
	'durations' => [],
] )

@php
	if ( empty( $durations ) || ! is_array( $durations ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-durations>
	<x-form.field-group>
		@foreach ( $durations as $filter_value => $filter_label )
			<x-dates-rates.filters.checkbox name="durations" :label="$filter_label" :value="$filter_value" data-label="{!! $filter_label !!}" />
		@endforeach
	</x-form.field-group>
</quark-dates-rates-filter-durations>
