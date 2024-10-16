@props( [
	'available_months' => [],
] )

@php
	if ( empty( $available_months ) ) {
		return;
	}
@endphp

<quark-search-filters-bar-departure-months-filter-options
	class="search-filters-bar__departure-months-filter-options"
	active="false"
	default-placeholder="{{ __( 'I\'m Flexible', 'qrk' ) }}"
>

	<div class="search-filters-bar__departure-months-filter-options-selector">
		<x-months-multi-select
			:available_months="$available_months"
			:is_multi_select="false"
		>
			<x-months-multi-select.slide :years="[ '2024', '2025' ]" />
			<x-months-multi-select.slide :years="[ '2026', '2027' ]" />
		</x-months-multi-select>
	</div>

	{{-- Filter options in accordion for mobile devices --}}
	<div class="search-filters-bar__departure-months-filter-options-accordion">
		<x-accordion>
			<x-accordion.item id="accordion-departure-months">
				<x-accordion.item-handle title="Departure" />
				<x-accordion.item-content>
					<x-months-multi-select
						:available_months="$available_months"
						:is_multi_select="false"
					>
						<x-months-multi-select.slide :years="[ '2024', '2025' ]" />
						<x-months-multi-select.slide :years="[ '2026', '2027' ]" />
					</x-months-multi-select>
				</x-accordion.item-content>
			</x-accordion.item>
		</x-accordion>
	</div>
</quark-search-filters-bar-departure-months-filter-options>