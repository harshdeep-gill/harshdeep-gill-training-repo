@props( [
	'images'         => [],
	'title'          => '',
	'cta_badge_text' => '',
	'cta'            => '',
	'departures'     => [],
] )

@php
	if ( empty( $images ) ) {
		return;
	}
@endphp

<x-product-departures-card>
	<x-product-departures-card.images :image_ids="$images ?? ''">
		<x-product-departures-card.badge-cta :text="$cta_badge_text ?? ''" />
	</x-product-departures-card.images>
	<x-product-departures-card.content>
		<x-product-departures-card.title :title="$title ?? ''" />
		<x-product-departures-card.cta>
			{!! $cta !!}
		</x-product-departures-card.cta>
		<x-product-departures-card.departures>
			<x-product-departures-card.overline :text="$departures['overline'] ?? ''" />
			@foreach( $departures['dates'] as $date )
				<x-product-departures-card.dates>
					<x-product-departures-card.departure-dates>
						{!! $date['dates'] ?? '' !!}
					</x-product-departures-card.departure-dates>
					<x-product-departures-card.offer
						:offer="$date['offer'] ?? ''"
						:offer_text="$date['offer_text'] ?? ''"
						:sold_out="$date['is_sold_out'] ?? false"
					/>
				</x-product-departures-card.dates>
			@endforeach
		</x-product-departures-card.departures>
	</x-product-departures-card.content>
</x-product-departures-card>
