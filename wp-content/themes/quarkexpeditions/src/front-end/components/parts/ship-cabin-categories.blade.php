@props( [
	'items' => []
] )

@php
if ( empty( $items ) || ! is_array( $items ) ) {
	return;
}
@endphp

<div class="travelopia-table">
	<table>
		<thead class="travelopia-table__row-container">
			<tr class="travelopia-table__row">
				<td class="travelopia-table__column">{{ __( 'CABIN CATEGORY', 'qrk' ) }}</td>
				<td class="travelopia-table__column">{{ __( 'DECK LOCATION(S)', 'qrk' ) }}</td>
			</tr>
		</thead>
		<tbody class="travelopia-table__row-container">
		@foreach ( $items as $item )
			<tr class="travelopia-table__row">
				<td class="travelopia-table__column">
					<strong>{{ $item['cabin_name'] }}</strong>
				</td>
				<td class="travelopia-table__column">{!! implode( '<br>', $item['ship_deck'] ) !!}</td>
			</tr>
		@endforeach
		</tbody>

	</table>
</div>
