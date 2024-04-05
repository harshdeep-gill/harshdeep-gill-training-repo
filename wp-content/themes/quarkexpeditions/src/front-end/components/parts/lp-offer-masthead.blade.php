@props( [
	'background_image_id' => 0,
	'logo_image_id'       => 0,
	'content'             => [],
] )

@php
	if ( empty( $background_image_id ) || empty( $content ) ) {
		return;
	}
@endphp

<x-lp-offer-masthead>
	<x-lp-offer-masthead.image :image_id="$background_image_id ?? 0" />
	<x-lp-offer-masthead.content>
		<x-lp-offer-masthead.logo :image_id="$logo_image_id ?? 0" />
		@foreach ( $content as $item )
			@if ( 'offer-image' === $item['type'] )
				<x-lp-offer-masthead.offer-image :image_id="$item['offer_image_id'] ?? 0" />
			@endif

			@if ( 'caption' === $item['type'] )
				<x-lp-offer-masthead.caption>
					{!! $item['caption'] ?? '' !!}
				</x-lp-offer-masthead.caption>
			@endif

			@if ( 'inner-content' === $item['type'] )
				<x-lp-offer-masthead.inner-content>
					{!! $item['inner_content'] ?? '' !!}
				</x-lp-offer-masthead.inner-content>
			@endif
		@endforeach
	</x-lp-offer-masthead.content>
</x-lp-offer-masthead>
