@props( [
	'results_count' => 0,
	'payload'       => [],
	'cards'         => [],
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
		selector=".departure-cards"
		:payload="$payload ?? []"
	>
		{{-- TODO: Render Departure Cards. --}}
		<div class="departure-cards"></div>
	</x-book-departures-expeditions.results>
  </x-book-departures-expeditions>
