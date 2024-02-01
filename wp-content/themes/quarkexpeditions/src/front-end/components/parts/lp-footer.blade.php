@props( [
	'columns' => [],
] )

@php
	if ( empty( $columns ) ) {
		return;
	}

@endphp

<x-lp-footer>
	<x-lp-footer.columns>
		@foreach( $columns as $column )
			<x-lp-footer.column>
				{!! $column !!}
			</x-lp-footer.column>
		@endforeach
	</x-lp-footer.columns>
</x-lp-footer>
