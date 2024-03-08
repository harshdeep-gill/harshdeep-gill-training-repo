@props( [
	'is_compact' => false,
	'image_id'   => 0,
	'content'    => [],
] )

@php
	if ( empty( $image_id ) || empty( $content ) ) {
		return;
	}
@endphp

<x-media-content-card :is_compact="$is_compact">
	<x-media-content-card.image :image_id="$image_id" />
	<x-media-content-card.content>
		@foreach ( $content as $column )
			<x-media-content-card.content-column>
				@if ( ! empty( $column['slot' ] ) )
					<x-content :content="$column['slot']" />
				@endif
				@if ( ! empty( $column['content_info' ] ) )
					@foreach ( $column['content_info' ] as $info )
						<x-media-content-card.content-info
							:label="$info['label']"
							:value="$info['value']"
							:url="$info['url']"
							:target="$info['target']"
						/>
					@endforeach
				@endif
			</x-media-content-card.content-column>
		@endforeach
	</x-media-content-card.content>
</x-media-content-card>
