@props( [
	'ships' => [],
] )

@php
	if ( empty( $ships ) || ! is_array( $ships ) ) {
		return;
	}
@endphp

<quark-expedition-search-filter-ships>
	<x-accordion.item>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Ships', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $ships as $ship )
					@if ( empty( $ship['label'] ) || empty( $ship['value'] ) || ! isset( $ship['count'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="ships" :label="$ship['label']" :value="$ship['value']" :count="$ship['count']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
	</x-accordion.item>
</quark-expedition-search-filter-ships>
