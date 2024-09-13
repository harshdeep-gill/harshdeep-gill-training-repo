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

		@foreach ( $months as $month )
			@if ( empty( $month['label'] ) || empty( $month['value'] ) || ! isset( $month['count'] ) )
				@continue
			@endif

			<x-dates-rates.filters.checkbox name="months" :label="$month['label']" :value="$month['value']" :count="$month['count']" />
		@endforeach

	</x-form.field-group>
</quark-dates-rates-filter-departure-months>
