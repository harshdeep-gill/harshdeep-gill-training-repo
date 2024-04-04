@props( [
	'background_image_id' => 0,
	'logo_image_id'       => 0,
	'offer_image_id'      => 0,
	'caption'             => '',
	'inner_content'       => '',
] )

@php
	if ( empty( $background_image_id ) ) {
		return;
	}
@endphp

<x-lp-offer-masthead>
	<x-lp-offer-masthead.image :image_id="$background_image_id ?? 0" />
	<x-lp-offer-masthead.content>
		<x-lp-offer-masthead.logo :image_id="$logo_image_id ?? 0" />
		<x-lp-offer-masthead.offer-image :image_id="$offer_image_id ?? 0" />
		<x-lp-offer-masthead.caption>
			{!! $caption ?? '' !!}
		</x-lp-offer-masthead.caption>
		<x-lp-offer-masthead.inner-content>
			{!! $inner_content ?? '' !!}
		</x-lp-offer-masthead.inner-content>
	</x-lp-offer-masthead.content>
</x-lp-offer-masthead>
