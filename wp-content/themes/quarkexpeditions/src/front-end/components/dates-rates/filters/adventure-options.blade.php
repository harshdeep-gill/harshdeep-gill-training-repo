@props( [
	'adventure_options' => [],
] )

@php
	if ( empty( $adventure_options ) || ! is_array( $adventure_options ) ) {
		return;
	}
@endphp

<quark-dates-rates-filter-adventure-options>
	<x-form.field-group>

		@foreach ( $adventure_options as $adventure_option )
			@if ( empty( $adventure_option['label'] ) || empty( $adventure_option['value'] ) || ! isset( $adventure_option['count'] ) )
				@continue
			@endif

			<x-dates-rates.filters.checkbox name="adventure_options" :label="$adventure_option['label']" :value="$adventure_option['value']" :count="$adventure_option['count']" />
		@endforeach

	</x-form.field-group>
</quark-dates-rates-filter-adventure-options>
