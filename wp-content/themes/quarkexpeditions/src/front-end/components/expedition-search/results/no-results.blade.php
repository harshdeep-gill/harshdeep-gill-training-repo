<div class="expedition-search__no-results">
	<p class="h3 expedition-search__no-results-heading"><x-escape :content="__( 'No expeditions found', 'qrk' )" /></p>
	<p class="expedition-search__no-results-description">
		<x-escape :content="__( 'Looks like we can\'t seem to find what you are looking for. Please try resetting or changing the filters.' )" />
	</p>
	<x-expedition-search.sidebar-filters.cta-clear :text="__( 'Reset Filters', 'qrk' )" />
</div>
