@props( [
	'rowspan'     => '',
	'colspan'     => '',
	'is_sold_out' => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'dates-rates__item-table-column' ];

	if ( true === $is_sold_out ) {
		$classes[] = 'dates-rates__item-table-column--sold-out';
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
