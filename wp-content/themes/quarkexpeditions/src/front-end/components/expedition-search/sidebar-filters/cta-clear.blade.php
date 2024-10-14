@props( [
	'text' => __( 'Clear all', 'qrk' ),
] )

<quark-expedition-search-filters-cta-clear-all>
	<x-button
		size="big"
		appearance="outline"
		class="expedition-search__cta-clear-filters"
	>
		<x-escape :content="$text" />
	</x-button>
</quark-expedition-search-filters-cta-clear-all>
