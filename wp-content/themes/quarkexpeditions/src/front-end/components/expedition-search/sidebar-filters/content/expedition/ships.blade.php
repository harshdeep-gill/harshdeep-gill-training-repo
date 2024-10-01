@props( [
	'ships' => [],
] )

@php
	if ( empty( $ships ) || ! is_array( $ships ) ) {
		return;
	}
@endphp

<x-accordion.item>
	<x-accordion.item-handle title="{{ __( 'Ships', 'qrk' ) }}" />
	<x-accordion.item-content>
	<quark-expedition-search-filter-ships>
		<x-form.field-group>
			@foreach ( $ships as $ship )
				@if ( empty( $ship['label'] ) || empty( $ship['value'] ) || ! isset( $ship['count'] ) )
					@continue
				@endif

				<x-expedition-search.sidebar-filters.checkbox name="ships" :label="$ship['label']" :value="$ship['value']" :count="$ship['count']" />
			@endforeach
		</x-form.field-group>
	</quark-expedition-search-filter-ships>
	</x-accordion.item-content>
</x-accordion.item>
