@props( [
	'results_count' => 0,
	'cards'         => [],
	'ship_id'       => 0,
] )

<x-book-departures-ships>
	{{-- Header --}}
	<x-book-departures-ships.header>
		<x-book-departures-ships.result-count count="{{ $results_count }}" />
		<x-book-departures-ships.filters />
	</x-book-departures-ships.header>

	{{-- Results --}}
	<x-book-departures-ships.results
		:count="$results_count"
		:ship_id="$ship_id ?? 0"
	>
		<x-parts.ship-departure-cards :cards="$cards ?? []" />
	</x-book-departures-ships.results>
</x-book-departures-ships>
