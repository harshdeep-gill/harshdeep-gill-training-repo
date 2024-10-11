@props( [
	'count'   => 0,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Build load more button text.
	$load_more_text = __( 'View More Expeditions', 'qrk' );
@endphp

<x-expedition-search.load-more :load_more_text="$load_more_text">
	<quark-expedition-search-results
		class="expedition-search__results"
		partial='expedition-search'
		selector='.expedition-cards'
	>
		{!! $slot !!}
	</quark-expedition-search-results>
	<div class="expedition-search__results--loading">
		<x-svg name="spinner" />
	</div>
</x-expedition-search.load-more>
