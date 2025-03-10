@props( [
	'layout'             => '',
	'is_mobile_carousel' => false,
	'has_offer_tag'      => false,
	'cards'              => [],
	'pagination'         => '',
	'current_page'       => 0,
	'total_pages'        => 0,
	'first_page_link'    => '',
	'last_page_link'     => '',
] )

@php
	if ( empty( $cards ) ) {
		return;
	}

@endphp

<x-info-cards :layout="$layout" :mobile_carousel="$is_mobile_carousel">
	@foreach ( $cards as $card )
		<x-info-cards.card size="big" :url="$card['permalink'] ?? ''">
			<x-info-cards.image :image_id="$card['featured_image'] ?? 0" />
			<x-info-cards.content position="bottom">
				<x-info-cards.overline>
					@php
						$read_time = sprintf( _n( '%d min read', '%d mins read', $card['read_time'], 'qrk' ), $card['read_time'] );
					@endphp
					@if ( ! empty( $read_time ) )
						<x-escape :content="$read_time" />
					@endif
				</x-info-cards.overline>
				<x-info-cards.title :title="$card['title'] ?? ''" />
				@if ( $loop->first )
					<x-info-cards.description>
						{!! $card['excerpt'] ?? '' !!}
					</x-info-cards.description>
				@endif
				<x-info-cards.cta text="{{ __( 'Read Post', 'qrk' ) }}" />
			</x-info-cards.content>
		</x-info-cards.card>
	@endforeach
</x-info-cards>

<x-parts.pagination :pagination="$pagination" :current_page="$current_page" :total_pages="$total_pages" :first_page_link="$first_page_link" :last_page_link="$last_page_link" />
