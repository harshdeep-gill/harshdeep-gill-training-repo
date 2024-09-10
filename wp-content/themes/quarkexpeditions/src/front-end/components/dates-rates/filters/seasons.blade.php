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

		@foreach ( $seasons as $season )
			@if ( empty( $season['label'] ) || empty( $season['value'] ) || ! isset( $season['count'] ) ) 
				@continue
			@endif

			<x-dates-rates.filters.checkbox name="seasons" :label="$season['label']" :value="$season['value']" :count="$season['count']" data-label="{{ $season['label'] }}" />
		@endforeach
	</x-form.field-group>
</quark-dates-rates-filter-seasons>
