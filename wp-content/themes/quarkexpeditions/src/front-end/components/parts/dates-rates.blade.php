@props( [
	'results_count'   => 0,
	'remaining_count' => 0,
	'cards'           => [],
	'expedition_id'   => 0,
] )

<x-dates-rates>
	{{-- Header --}}
	<x-dates-rates.header>
		<x-dates-rates.filters />
		<x-dates-rates.result.count count="{{ $results_count }}" />
	</x-dates-rates.header>

	{{-- Results --}}
	<x-dates-rates.results
		:count="$remaining_count"
		:expedition_id="$expedition_id ?? 0"
	>
		<x-parts.dates-rates-cards :cards="$cards ?? []" />
	</x-dates-rates.results>
</x-dates-rates>
