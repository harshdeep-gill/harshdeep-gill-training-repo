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
			@foreach ($row as $column)
				<x-lp-footer.column url="{{ $column['url'] }}">
					@foreach ($column['contents'] as $column_content)
						@if ( 'icon' === $column_content['type'] )
							<x-lp-footer.icon name="{{ $column_content['attributes']['name'] }}"/>
						@elseif ( 'social-links' === $column_content['type'] )
							<x-parts.social-links :links="$column_content['attributes']['links']"/>
						@elseif ( 'block' === $column_content['type'] )
							{!! $column_content['attributes']['content'] !!}
						@endif
					@endforeach
				</x-lp-footer.column>
			@endforeach
		</x-lp-footer.row>
	@endforeach
</x-lp-footer>
