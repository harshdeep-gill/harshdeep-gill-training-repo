@props( [
	'cards'           => [],
	'layout'          => 'grid',
	'showSeason'      => true,
	'showTitle'       => true,
	'showRole'        => true,
	'showCta'         => true,
	'pagination'      => '',
	'current_page'    => 0,
	'total_pages'     => 0,
	'first_page_link' => '',
	'last_page_link'  => '',
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-info-cards :layout="$layout">
	@foreach ( $cards as $card )
		<x-info-cards.card size="big" :url="$card['permalink'] ?? ''">
			<x-info-cards.image :image_id="$card['featured_image'] ?? 0" />
			<x-info-cards.content position="bottom">
				@if ( ! empty( $card['season'] ) && $showSeason )
					<x-info-cards.overline>
						<x-escape :content="$card['season']" />
					</x-info-cards.overline>
				@endif
				@if ( ! empty( $card['title'] ) && $showTitle )
					<x-info-cards.title :title="$card['title']" />
				@endif
				@if ( ! empty( $card['role'] ) && $showRole )
					<x-info-cards.description>
						<x-escape :content="$card['role']" />
					</x-info-cards.description>
				@endif
				@if ( $showCta )
					<x-info-cards.cta :text="__( 'Read more', 'qrk' )" />
				@endif
			</x-info-cards.content>
		</x-info-cards.card>
	@endforeach
</x-info-cards>

<x-parts.pagination :pagination="$pagination" :current_page="$current_page" :total_pages="$total_pages" :first_page_link="$first_page_link" :last_page_link="$last_page_link" />
