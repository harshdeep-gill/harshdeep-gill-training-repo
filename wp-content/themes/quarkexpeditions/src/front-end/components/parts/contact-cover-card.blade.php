@props( [
	'image_id'   => 0,
	'content'    => [],
] )

@php
	if ( empty( $image_id ) || empty( $content ) ) {
		return;
	}
@endphp

<x-contact-cover-card>
	<x-contact-cover-card.image :image_id="$image_id ?? 0" />
	<x-contact-cover-card.content>
		@foreach ( $content as $item )
			@if ( 'title' === $item['type'] )
				<x-contact-cover-card.title :title="$item['title'] ?? ''" />
			@endif

			@if ( 'description' === $item['type'] )
				<x-contact-cover-card.description>
					{!! $item['description'] ?? '' !!}
				</x-contact-cover-card.description>
			@endif

			@if ( 'contact-info' === $item['type'] )
				<x-contact-cover-card.contact-info>
					@if ( ! empty( $item['children'] ) )
						@foreach ( $item['children'] as $info_item )
							<x-contact-cover-card.contact-info-item
								:label="$info_item['label'] ?? ''"
								:value="$info_item['value'] ?? ''"
								:url="$info_item['url'] ?? ''"
							/>
						@endforeach
					@endif
				</x-contact-cover-card.contact-info>
			@endif
		@endforeach
	</x-contact-cover-card.content>
</x-contact-cover-card>
