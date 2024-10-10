@props( [
	'itinerary_lengths' => [],
] )

@php
	if ( empty( $itinerary_lengths ) || ! is_array( $itinerary_lengths ) ) {
		return;
	}

	$itinerary_length = array_reduce(
		$itinerary_lengths,
		function( $acc, $item ) {
			// Base case.
			$acc = [
				'max' => $acc['max'] ?? PHP_INT_MIN,
				'min' => $acc['min'] ?? PHP_INT_MAX,
			];

			// Null check.
			if ( empty( $item['value'] ) ) {
				// Bail.
				return $acc;
			}

			return [
				'max' => max( $acc['max'], $item['value'] ),
				'min' => min( $acc['min'], $item['value'] ),
			];
		}
	);

	if ( empty( $itinerary_length ) || 0 >= $itinerary_length[ 'min' ] || $itinerary_length['min'] > $itinerary_length['max'] ) {
		return;
	}
@endphp

<quark-expedition-search-filter-itinerary-lengths>
	<x-accordion.item>
		<x-accordion.item-handle>
			<x-escape :content=" __( 'Itinerary Lengths', 'qrk' ) " />
		</x-accordion-item.handle>
		<x-accordion.item-content>
			<x-form.range-slider name="itinerary-lengths" :range_suffix="_( 'Days', 'qrk' )" :min="$itinerary_length['min']" :max="$itinerary_length['max']" />
		</x-accordion.item-content>
</x-accordion.item>
</quark-expedition-search-filter-itinerary-lengths>
