@props( [
	'months' => [],
] )

@php
	if ( empty( $months ) || ! is_array( $months ) ) {
		return;
	}
@endphp

<quark-expedition-search-filter-months>
	<x-accordion.item>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Months', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.field-group>
				@foreach ( $months as $month )
					@if ( empty( $month['label'] ) || empty( $month['value'] ) || ! isset( $month['count'] ) )
						@continue
					@endif

					<x-expedition-search.sidebar-filters.checkbox name="months" :label="$month['label']" :value="$month['value']" :count="$month['count']" />
				@endforeach
			</x-form.field-group>
		</x-accordion.item-content>
</x-accordion.item>
</quark-expedition-search-filter-months>
