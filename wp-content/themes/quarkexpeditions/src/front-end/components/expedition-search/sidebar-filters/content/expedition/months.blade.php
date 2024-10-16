@props( [
	'months'     => [],
	'is_compact' => false,
] )

@php
	if ( empty( $months ) || ! is_array( $months ) ) {
		return;
	}

	$years = [];

	foreach ( $months as $month ) {
		$year = explode( '-', $month['value'] );

		if ( 2 === count( $year ) ) {
			$years[] = absint( $year[1] );
		}
	}

	$years = array_unique( $years );
@endphp

<quark-expedition-search-filter-months>
	<x-accordion.item>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Months', 'qrk' ) " /> <span class="expedition-search__filter-count"></span>
		</x-accordion-item.handle>
		<x-accordion.item-content>
			@if ( ! empty( $is_compact ) )
				<x-months-multi-select
					:available_months="$months"
					:is_multi_select="true"
				>
					<x-months-multi-select.carousel :is_compact="true">
							@foreach ( $years as $year )
								<x-months-multi-select.slide :years="[ $year ]" :is_compact="true" />
							@endforeach
					</x-months-multi-select.carousel>
				</x-months-multi-select>
			@else
				<x-months-multi-select
					:available_months="$months"
					:is_multi_select="true"
				>
					<x-months-multi-select.carousel>
						@foreach ( $years as $year )
							<x-months-multi-select.slide :years="[ $year ]" />
						@endforeach
					</x-months-multi-select.carousel>
				</x-months-multi-select>
			@endif

		</x-accordion.item-content>
</x-accordion.item>
</quark-expedition-search-filter-months>
