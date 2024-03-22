@props( [
	'image_id'       => 0,
	'cta_badge_text' => '',
	'media_type'     => 'image',
	'media_align'    => 'left',
	'video_url'      => '',
	'content'        => [],
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<x-media-text-cta :media_align="$media_align">
	@if ( 'image' === $media_type )
		<x-media-text-cta.image :image_id="$image_id">
			<x-media-text-cta.badge :text="$cta_badge_text ?? ''" />
		</x-media-text-cta.image>
	@endif

	@if ( 'video' === $media_type )
		<x-media-text-cta.video>
			<x-fancy-video :url="$video_url" :image_id="$image_id" />
			<x-media-text-cta.badge :text="$cta_badge_text ?? ''" />
		</x-media-text-cta.video>
	@endif

	<x-media-text-cta.content>
		@foreach ( $content as $item )
			@if ( 'slot' === $item['type'] )
				{!! $item['slot'] !!}
			@endif

			@if ( 'secondary-text' === $item['type'] )
				<x-media-text-cta.secondary-text :text="$item['text'] ?? ''" />
			@endif

			@if ( 'cta' === $item['type'] && ! empty( $item['cta']) )
				<x-media-text-cta.cta>
					{!! $item['cta'] ?? '' !!}
				</x-media-text-cta.cta>
			@endif
		@endforeach
	</x-media-text-cta.content>
</x-media-text-cta>