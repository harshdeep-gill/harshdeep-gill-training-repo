@props( [
	'results_count' => 0,
	'cards'         => [],
	'expedition_id' => 0,
] )

<x-book-departures-expeditions>
	{{-- Header --}}
	<x-book-departures-expeditions.header>
	  <x-book-departures-expeditions.result-count count="{{ $results_count }}" />
	  <x-book-departures-expeditions.filters />
	</x-book-departures-expeditions.header>

	{{-- Results --}}
	<x-book-departures-expeditions.results
		:count="$results_count"
		partial="book-departures-expeditions"
		selector=".book-departures-expeditions__results"
		:expedition_id="$expedition_id ?? 0"
		:payload="$payload ?? []"
	>
		<x-parts.expedition-departure-cards :cards="$cards ?? []" />
	</x-book-departures-expeditions.results>
</x-book-departures-expeditions>
