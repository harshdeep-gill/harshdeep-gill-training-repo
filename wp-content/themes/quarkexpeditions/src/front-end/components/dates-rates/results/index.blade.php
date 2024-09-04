@props( [
	'count'         => 0,
	'total_count'   => 0,
	'expedition_id' => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp


<div class="dates-rates__results-container section">
	<x-dates-rates.results.count :count="$count" :total_count="$total_count" />
	<quark-dates-rates-results
		class="dates-rates__results"
		partial='dates-and-rates'
		selector='.dates-rates__cards'
		expedition-id={{ $expedition_id }}
	>
		{!! $slot !!}
	</quark-dates-rates-results>
	<div class="dates-rates__results--loading">
		<x-svg name="spinner" />
	</div>
</div>

