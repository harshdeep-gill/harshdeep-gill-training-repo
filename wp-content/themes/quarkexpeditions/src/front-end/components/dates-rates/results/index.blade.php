@props( [
	'page'         => 1,
	'total_pages'  => 1,
	'per_page'     => 10,
	'result_count' => 0
] )

@php
	$is_server_rendered = false;

	if ( ! empty( $result_count ) && ! empty( $slot ) ) {
		$is_server_rendered = true;
	}

@endphp

<div class="dates-rates__results-container section">
	<quark-dates-rates-results
		class="dates-rates__results"
		partial='dates-and-rates'
		selector='.dates-rates__cards'
		@if ( $is_server_rendered )
			page="{{ $page }}"
			total-pages="{{ $total_pages }}"
			per-page="{{ $per_page }}"
			result-count="{{ $result_count }}"
			server-rendered="yes"
		@endif
	>
		{!! $slot !!}
	</quark-dates-rates-results>
	<div class="dates-rates__results--loading">
		<x-svg name="spinner" />
	</div>
</div>

