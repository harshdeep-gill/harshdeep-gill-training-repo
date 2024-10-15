@props( [
	'remaining_count' => 0,
	'results_count'   => 0,
	'page'            => 1,
	'next_page'       => 1,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$is_server_rendered = false;

	if ( $remaining_count > 0 && $remaining_count <= $results_count ) {
		$is_server_rendered = true;
	}

	// Build load more button text.
	$load_more_text = __( 'View More Expeditions', 'qrk' );
@endphp

<x-expedition-search.load-more :load_more_text="$load_more_text">
	<quark-expedition-search-results
		class="expedition-search__results"
		partial='expedition-search'
		selector='.expedition-cards'

		@if ( $is_server_rendered )
			server-rendered="yes"
			remaining-count="{{ $remaining_count }}"
			results-count="{{ $results_count }}"
			page="{{ $page }}"
			next-page="{{ $next_page }}"
		@endif
	>
		{!! $slot !!}
	</quark-expedition-search-results>
	<div class="expedition-search__results--loading">
		<x-svg name="spinner" />
	</div>
</x-expedition-search.load-more>
