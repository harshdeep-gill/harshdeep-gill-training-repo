@props( [
	'count'       => 0,
	'total_count' => 0,
] )

@php
	if ( empty( $count ) || empty( $total_count ) || $count < 0 || $total_count < $count ) {
		return;
	}
@endphp

<div class="dates-rates__result-count">
	<quark-dates-rates-results-count class="dates-rates__result-count-values" total-count="{!! esc_attr( $total_count ) !!}">
		{{ __( 'Showing', 'qrk' ); }}
		<span class="dates-rates__result-count-value">{{ $count }}</span>
		<x-dates-rates.pagination.items-per-page class="dates-rates__result-count-selector" />
		{{ __( 'of total', 'qrk' ); }}
		<span class="dates-rates__result-count-total">{{ $total_count }}</span>
	</quark-dates-rates-results-count>

	<div class="dates-rates__result-count-annotations">
		<span class="dates-rates__result-count-annotation">{{ __( 'Standard Cabin', 'qrk' ) }}</span>
		<span class="dates-rates__result-count-annotation dates-rates__result-count-annotation--premium">{{ __( 'Premium Cabin', 'qrk' ) }}</span>
	</div>
</div>
