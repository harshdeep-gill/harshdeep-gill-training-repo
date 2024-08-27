@props( [
	'text' => __( 'Clear all', 'qrk' ),
] )

<x-button
	size="big"
	appearance="outline"
	class="dates-rates__cta-clear-filters"
>
	<x-escape :content="$text" />
</x-button>
