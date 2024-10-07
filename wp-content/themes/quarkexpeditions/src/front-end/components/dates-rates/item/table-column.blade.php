@props( [
	'rowspan'          => '',
	'colspan'          => '',
	'is_sold_out'      => false,
	'is_pay_in_full'   => false,
	'is_stock_limited' => false,
	'is_discounted'    => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'dates-rates__item-table-column' ];

	if ( true === $is_discounted ){
		$classes[] = 'dates-rates__item-table-column--discounted';
	}

	if ( true === $is_sold_out ) {
		$classes[] = 'dates-rates__item-table-column--sold-out';
	}

	if ( true === $is_pay_in_full ) {
		$classes[] = 'dates-rates__item-table-column--pay-in-full';
	}

	if ( true === $is_stock_limited ) {
		$classes[] = 'dates-rates__item-table-column--stock-limited';
	}
@endphp

<td
	@class( $classes )
	@if ( ! empty( $rowspan ) )
		rowspan="{{ $rowspan }}"
	@endif
	@if ( ! empty( $colspan ) )
		colspan="{{ $colspan }}"
	@endif
>
	{!! $slot !!}
</td>
