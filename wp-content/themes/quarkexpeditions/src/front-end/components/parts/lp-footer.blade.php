@props( [
	'rows' => [],
] )

@php
	if ( empty( $rows ) ) {
		return;
	}

@endphp

<x-lp-footer>
	@foreach ( $rows as $row)
		<x-lp-footer.row>
			@foreach ( $row as $column )
				<x-lp-footer.column>
					{!! $column !!}
				</x-lp-footer.column>
			@endforeach
		</x-lp-footer.row>
	@endforeach
</x-lp-footer>
