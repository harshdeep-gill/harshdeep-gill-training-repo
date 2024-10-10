@props( [
	'text' => __( 'Search Expeditions', 'qrk' ),
] )

<div class="search-filters-bar__search-button">
	<x-button size="big">
		<x-escape :content="$text" />
	</x-button>
</div>