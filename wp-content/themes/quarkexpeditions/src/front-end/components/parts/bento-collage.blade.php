@props( [
	'items' => [],
] )

@php
	if ( empty( $items ) || ! is_array( $items ) ) {
		return;
	}
@endphp

<x-bento-collage>
	@foreach ( $items as $item )
		<x-bento-collage.card size="{{ $item['size'] ?? '' }}" url="{{ $item['cta']['url'] ?? '' }}" target="{{ $item['cta']['target'] ?? '' }}">
			<x-bento-collage.image image_id="{{ $item['image_id'] ?? '' }}" />
			<x-bento-collage.content position="{{ $item['content_position'] ?? '' }}">
				<x-bento-collage.title title="{!! $item['title'] ?? '' !!}" />
				<x-bento-collage.description>
					<p>{{ $item['description'] ?? '' }}</p>
				</x-bento-collage.description>
				<x-bento-collage.cta text="{!! $item['cta']['text'] ?? '' !!}"/>
			</x-bento-collage.content>
		</x-bento-collage.card>
	@endforeach
</x-bento-collage>
