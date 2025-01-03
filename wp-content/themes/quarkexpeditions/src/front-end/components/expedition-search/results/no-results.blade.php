<div class="expedition-search__no-results">
	<p class="h3 expedition-search__no-results-heading"><x-escape :content="__( 'No Departures Found', 'qrk' )" /></p>
	<p class="expedition-search__no-results-description">
		<x-escape :content="__( 'It looks like we can\'t find what you are looking for. Please try resetting or changing the filters.', 'qrk' )" />
	</p>
	<x-expedition-search.sidebar-filters.cta-clear :text="__( 'Reset Filters', 'qrk' )" />
</div>
