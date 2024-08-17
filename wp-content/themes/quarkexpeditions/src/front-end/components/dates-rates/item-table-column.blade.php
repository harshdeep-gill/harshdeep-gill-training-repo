@props( [
	'rowspan' => '1',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}
@endphp

<td class="dates-rates__item-table-column" rowspan="{{ $rowspan }}">
	{!! $slot !!}
</td>
