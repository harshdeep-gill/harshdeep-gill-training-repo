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

		@foreach ( $expeditions as $expedition )
			@if ( empty( $expedition['label'] ) || empty( $expedition['value'] ) || ! isset( $expedition['count'] ) )
				@continue
			@endif

			<x-dates-rates.filters.checkbox name="expeditions" :label="$expedition['label']" :value="$expedition['value']" :count="$expedition['count']" />
		@endforeach

	</x-form.field-group>
</quark-dates-rates-filter-expeditions>
