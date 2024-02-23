@props( [
	'name'  => '',
	'items' => [],
] )

@php
	if ( empty( $name ) || empty( $items ) ) {
		return;
	}
@endphp

<x-collage :name="$name">
	@foreach ( $items as $item )
		@if( 'image' === $item['media_type'] )
			<x-collage.image
				:size="$item['size']"
				:image_id="$item['image_id']"
				:title="$item['title']"
			/>
		@else
			<x-collage.video
				:size="$item['size']"
				:image_id="$item['image_id']"
				:video_url="$item['video_url']"
				:title="$item['title']"
			/>
		@endif
	@endforeach
</x-collage>
