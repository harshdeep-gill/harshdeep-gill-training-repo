@props( [
	'top'    => [],
	'middle' => [],
	'bottom' => [],
] )

<x-footer>
	<x-footer.top>
		@foreach ( $top as $top_inner_content )
			@if ( 'column' === $top_inner_content['type'] )
				<x-parts.footer-column :column="$top_inner_content"/>
			@endif
		@endforeach
	</x-footer.top>

	<x-footer.middle>
		@foreach ( $middle as $middle_inner_content )
			@if ( 'column' === $middle_inner_content['type'] )
				<x-parts.footer-column :column="$middle_inner_content"/>
			@endif

			@if( 'navigation' === $middle_inner_content['type'] )
				<x-parts.footer-navigation :navigation="$middle_inner_content" />
			@endif
		@endforeach
	</x-footer.middle>

	<x-footer.bottom>
		@foreach ( $bottom as $bottom_inner_content )
			@if ( 'copyright' === $bottom_inner_content['type'] )
				<x-footer.copyright>
					@foreach ($bottom_inner_content['content'] as $nav_item)
						{!! $nav_item !!}
					@endforeach
				</x-footer.copyright>
			@endif
			@if ( 'navigation' === $bottom_inner_content['type'] )
				<x-parts.footer-navigation :navigation="$bottom_inner_content" />
			@endif
		@endforeach
	</x-footer.bottom>
</x-footer>
