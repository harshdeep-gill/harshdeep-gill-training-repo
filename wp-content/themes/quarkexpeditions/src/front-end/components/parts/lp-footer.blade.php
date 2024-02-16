@props( [
	'row_slots' => [],
] )

@php
	if ( empty( $row_slots ) ) {
		return;
	}
@endphp

<x-lp-footer>
	@foreach ( $row_slots as $row_slot)
		<x-lp-footer.row>
			{!! $row_slot !!}
		</x-lp-footer.row>
	@endforeach
</x-lp-footer>
