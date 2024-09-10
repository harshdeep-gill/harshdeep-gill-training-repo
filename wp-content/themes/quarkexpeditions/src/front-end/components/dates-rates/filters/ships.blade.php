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

		@foreach ( $ships as $ship )
			@if ( empty( $ship['label'] ) || empty( $ship['value'] ) || ! isset( $ship['count'] ) )
				@continue
			@endif

			<x-dates-rates.filters.checkbox name="ships" :label="$ship['label']" :value="$ship['value']" :count="$ship['count']" data-label="{{ $ship['label'] }}" />
		@endforeach

	</x-form.field-group>
</quark-dates-rates-filter-ships>
