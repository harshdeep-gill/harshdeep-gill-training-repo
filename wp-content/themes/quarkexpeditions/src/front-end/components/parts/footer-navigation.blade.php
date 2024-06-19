@props( [
	'navigation' => [
		'type' => '',
	],
] )

@if ( ! empty( $navigation['type'] ) && 'navigation' === $navigation['type'] )
	<x-footer.navigation :title="$navigation['attributes']['title']">
		@foreach ($navigation['content'] as $maybe_nav_item)
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
