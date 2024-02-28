@props( [
	'seasons' => [],
] )

@php
	if ( empty( $seasons ) ) {
		return;
	}
@endphp

<x-season-highlights>
	@foreach ( $seasons as $season )
		@if ( ! empty( $season['items'] ) )
			<x-season-highlights.season :title="$season['title']">
					@foreach ( $season['items'] as $item )
						<x-season-highlights.item :title="$item['title']" :light="$item['light']">
							@if ( ! empty( $item['highlights'] ) )
								@foreach ( $item['highlights'] as $highlight )
									<x-season-highlights.highlight
										:icon="$highlight['icon']"
										:title="$highlight['title']"
									/>
								@endforeach
							@endif
						</x-season-highlights.item>
					@endforeach
			</x-season-highlights.season>
		@endif
	@endforeach
</x-season-highlights>
