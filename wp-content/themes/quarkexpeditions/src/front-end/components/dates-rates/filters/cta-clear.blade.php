@props( [
	'text' => __( 'Clear all', 'qrk' ),
] )

<quark-dates-rates-filters-cta-clear-all>
	<x-button
		size="big"
		appearance="outline"
		class="dates-rates__cta-clear-filters"
	>
		<x-escape :content="$text" />
	</x-button>
</quark-dates-rates-filters-cta-clear-all>
