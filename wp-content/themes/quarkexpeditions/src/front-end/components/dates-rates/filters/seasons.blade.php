@props( [
	'seasons' => [],
] )

@php
	if ( empty( $seasons ) || ! is_array( $seasons ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-seasons>
	<x-form.field-group>
		@foreach ( $seasons as $filter_value => $filter_label )
			<x-dates-rates.filters.checkbox name="seasons" :label="$filter_label" :value="$filter_value" data-label="{{ $filter_label }}" />
		@endforeach
	</x-form.field-group>
</quark-dates-rates-filter-seasons>
