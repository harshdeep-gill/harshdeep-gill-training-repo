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

		@foreach ( $durations as $duration )
			@if ( empty( $duration['label'] ) || empty( $duration['value'] ) || ! isset( $duration['count'] ) )
				@continue
			@endif

			<x-dates-rates.filters.checkbox name="durations" :label="$duration['label']" :value="$duration['value']" :count="$duration['count']" />
		@endforeach

	</x-form.field-group>
</quark-dates-rates-filter-durations>
