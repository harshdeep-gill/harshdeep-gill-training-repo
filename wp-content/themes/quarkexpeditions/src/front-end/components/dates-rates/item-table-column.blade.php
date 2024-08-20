@props( [
	'rowspan' => '',
	'colspan' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<td
	class="dates-rates__item-table-column"
	@if ( ! empty( $rowspan ) )
		rowspan="{{ $rowspan }}"
	@endif
	@if ( ! empty( $colspan ) )
		colspan="{{ $colspan }}"
	@endif
>
	{!! $slot !!}
</td>
