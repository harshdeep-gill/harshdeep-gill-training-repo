@props( [
	'count'       => 0,
	'total_count' => 0,
] )

@php
	if ( empty( $count ) || empty( $total_count ) ) {
		return;
	}
@endphp

<div class="dates-rates__result-count">
	<div class="dates-rates__result-count-values">
		{{ __( 'Showing', 'qrk' ); }}
		<span class="dates-rates__result-count-value">{{ $count ?? 0 }}</span>
		{{ __( 'of total', 'qrk' ); }}
		<span class="dates-rates__result-count-value">{{ $total_count ?? 0 }}</span>
	</div>

	<div class="dates-rates__result-count-annotations">
		<span class="dates-rates__result-count-annotation">{{ __( 'Standard Cabin', 'qrk' ) }}</span>
		<span class="dates-rates__result-count-annotation dates-rates__result-count-annotation--premium">{{ __( 'Premium Cabin', 'qrk' ) }}</span>
	</div>
</div>
