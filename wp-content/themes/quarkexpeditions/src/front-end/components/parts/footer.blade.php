@props( [
	'top'    => [],
	'middle' => [],
	'bottom' => [],
] )

<x-footer>
	<x-footer.top>
		@foreach ( $top as $top_inner_content )
			@if ( ! empty( $top_inner_content['type'] ) && 'column' === $top_inner_content['type'] )
				<x-footer.column :url="$top_inner_content['attributes']['url']">
					@foreach ( $top_inner_content['content'] as $column_inner_content )
						@switch ( $column_inner_content['type'] )
							@case ( 'title' )
								<x-footer.column-title :title="$column_inner_content['attributes']['title']" />
							@break

							@case ( 'icon' )
								<x-footer.icon :name="$column_inner_content['attributes']['name']" />
								@break

							@case ( 'social-links' )
								<x-footer.social-links :social_links="$column_inner_content['attributes']['social_links']" />
								@break

							@case ( 'payment-options' )
								<x-footer.payment-options />
							@break

							@case ( 'associations' )
								<x-footer.associations :association_links="$column_inner_content['attributes']['association_links']" />
							@break

							@case ( 'logo' )
								<x-footer.logo />
							@break

							@default
								{!! $column_inner_content['content'] !!}

						@endswitch
					@endforeach
				</x-footer.column>
			@endif
		@endforeach
	</x-footer.top>

	<x-footer.middle>
		@foreach ( $middle as $middle_inner_content )
			@if ( ! empty( $middle_inner_content['type'] ) && 'column' === $middle_inner_content['type'] )
				<x-footer.column :url="$middle_inner_content['attributes']['url']">
					@foreach ( $middle_inner_content['content'] as $column_inner_content )
						@switch ( $column_inner_content['type'] )
							@case ( 'title' )
								<x-footer.column-title :title="$column_inner_content['attributes']['title']" />
							@break

							@case ( 'icon' )
								<x-footer.icon :name="$column_inner_content['attributes']['name']" />
								@break

							@case ( 'social-links' )
								<x-footer.social-links :social_links="$column_inner_content['attributes']['social_links']" />
								@break

							@case ( 'payment-options' )
								<x-footer.payment-options />
							@break

							@case ( 'associations' )
								<x-footer.associations :association_links="$column_inner_content['attributes']['association_links']" />
								@php
									var_dump( $column_inner_content );
								@endphp
							@break

							@case ( 'logo' )
								<x-footer.logo />
							@break

							@default
								{!! $column_inner_content['content'] !!}

						@endswitch
					@endforeach
				</x-footer.column>
			@endif

			@if( ! empty( $middle_inner_content['type'] ) && 'navigation' === $middle_inner_content['type'] )
				<x-footer.navigation :title="$middle_inner_content['attributes']['title']">
					@foreach ( $middle_inner_content['content'] as $maybe_nav_item )
						@if ( 'navigation-item' === $maybe_nav_item['type'] )
							<x-footer.navigation-item
								:title="$maybe_nav_item['attributes']['title']"
								:url="$maybe_nav_item['attributes']['url']"
								:target="$maybe_nav_item['attributes']['target']"
							/>
						@endif
					@endforeach
				</x-footer.navigation>
			@endif
		@endforeach
	</x-footer.middle>

	<x-footer.bottom>
		@foreach ( $bottom as $bottom_inner_content )
			@if ( ! empty( $bottom_inner_content['type'] ) && 'copyright' === $bottom_inner_content['type'] )
				<x-footer.copyright>
					@foreach ( $bottom_inner_content['content'] as $nav_item )
						{!! $nav_item !!}
					@endforeach
				</x-footer.copyright>
			@endif
			@if ( ! empty( $bottom_inner_content['type'] ) && 'navigation' === $bottom_inner_content['type'] )
				<x-footer.navigation :title="$bottom_inner_content['attributes']['title']">
					@foreach ( $bottom_inner_content['content'] as $maybe_nav_item )
						@if ( 'navigation-item' === $maybe_nav_item['type'] )
							<x-footer.navigation-item
								:title="$maybe_nav_item['attributes']['title']"
								:url="$maybe_nav_item['attributes']['url']"
								:target="$maybe_nav_item['attributes']['target']"
							/>
						@endif
					@endforeach
				</x-footer.navigation>
			@endif
		@endforeach
	</x-footer.bottom>
</x-footer>
