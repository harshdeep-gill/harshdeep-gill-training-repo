@props( [
	'cards'                => [],
	'result_count_total'   => 0,
	'result_count_current' => 0,
	'pagination'           => '',
	'current_page'         => 0,
	'total_pages'          => 0,
] )

<x-press-releases>
	<x-press-releases.result-count :current="$result_count_current" :total="$result_count_total" />
	<x-press-releases.results>
		<x-listing-cards>
			@foreach( $cards as $card )
				<x-listing-cards.card>
					@if( ! empty( $card['overline'] ) )
						<x-listing-cards.overline :text="$card['overline']" />
					@endif

					<x-listing-cards.title :title="$card['title'] ?? ''" />

					@if( ! empty( $card['subtitle'] ) )
						<x-listing-cards.subtitle :subtitle="$card['subtitle']" />
					@endif

					@if( ! empty( $card['description'] ) )
						<x-listing-cards.description>
							{!! $card['description'] !!}
						</x-listing-cards.description>
					@endif
					<x-listing-cards.cta>
						<x-button :href="$card['permalink'] ?? ''" size="big" color="black">Read More</x-button>
					</x-listing-cards.cta>
				</x-listing-cards.card>
			@endforeach
		</x-listing-cards>
	</x-press-releases.results>
	<x-pagination>
		<x-pagination.total-pages :current_page="$current_page" :total_pages="$total_pages" />
		<x-pagination.links>
			@if ( ! empty( $first_page_link ) )
				<x-pagination.first-page :href="$first_page_link" >First</x-pagination.first-page>
			@endif
			{!! $pagination !!}
			@if ( ! empty( $last_page_link ) )
				<x-pagination.last-page :href="$last_page_link" >Last</x-pagination.last-page>
			@endif
		</x-pagination.links>
	</x-pagination>
</x-press-releases>
