@props( [
	'cards'                => [],
	'result_count_total'   => 0,
	'result_count_current' => 0,
	'pagination'           => '',
	'current_page'         => 0,
	'total_pages'          => 0,
	'first_page_link'      => '',
	'last_page_link'       => '',
] )

<x-press-releases>
	<x-press-releases.result-count :current="$result_count_current" :total="$result_count_total" />
	<x-listing-cards>
		@foreach( $cards as $card )
			<x-listing-cards.card>
				@if( ! empty( $card['overline'] ) )
					<x-listing-cards.overline :text="$card['overline']" />
				@endif

				<x-listing-cards.title :title="$card['title'] ?? ''" />

				@if( ! empty( $card['description'] ) )
					<x-listing-cards.description>
						{!! $card['description'] !!}
					</x-listing-cards.description>
				@endif
				<x-listing-cards.cta>
					<x-button :href="$card['permalink'] ?? ''" size="big" color="black">{!! __( 'Read More', 'qrk' ) !!}</x-button>
				</x-listing-cards.cta>
			</x-listing-cards.card>
		@endforeach
	</x-listing-cards>
	<x-parts.pagination :pagination="$pagination" :current_page="$current_page" :total_pages="$total_pages" :first_page_link="$first_page_link" :last_page_link="$last_page_link" />
</x-press-releases>
