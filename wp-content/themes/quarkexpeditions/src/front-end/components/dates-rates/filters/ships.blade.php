@props( [
	'ships' => [],
] )

@php
	if ( empty( $ships ) || ! is_array( $ships ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-ships>
	<x-form.field-group>
		@foreach ( $ships as $filter_value => $filter_label )
			<x-dates-rates.filters.checkbox name="ships" :label="$filter_label" :value="$filter_value" data-label="{{ $filter_label }}" />
		@endforeach
	</x-form.field-group>
</quark-dates-rates-filter-ships>
