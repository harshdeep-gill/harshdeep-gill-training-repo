@props( [
	'columns' => [],
] )

@php
	if ( empty( $columns ) ) {
		return;
	}

@endphp

<x-lp-footer>
	<x-lp-footer.row>
		@foreach( $row as $column )
			<x-lp-footer.column>
				{!! $column !!}
			</x-lp-footer.column>
		@endforeach
	</x-lp-footer.row>
</x-lp-footer>
