@props( [
	'image_id'            => 0,
	'immersive'           => 'no',
	'content_overlap'     => false,
	'overlay_opacity'     => 0,
	'left'                => [],
	'right'               => [],
	'dark_mode'           => false,
	'hero_details_slider' => [],
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<x-search-hero :immersive="$immersive" :overlay_opacity="$overlay_opacity" :content_overlap="$content_overlap">
	<x-search-hero.image :image_id="$image_id" />
	<x-search-hero.content>
		@if ( ! empty( $left ) || is_array( $left ) )
			<x-search-hero.left>
				@foreach ( $left as $key => $item )
					@if ( 'title_container' === $key )
						@foreach ( $item as $text_item )
							@if ( 'overline' === $text_item['type'] && ! empty( $text_item['text'] ) )
								<x-search-hero.overline :color="$text_item['color']">
									{!! $text_item['text'] !!}
								</x-search-hero.overline>
							@endif

							@if ( 'title' === $text_item['type'] && ! empty( $text_item['text'] ) )
								<x-search-hero.title :title="$text_item['text']" :color="$text_item['color']" :use_promo_font="$text_item['use_promo_font']" />
							@endif

							@if ( 'title_bicolor' === $text_item['type'] )
								@if ( ! empty( $text_item['white_text'] && ! empty( $text_item['yellow_text'] ) ) )
									<x-search-hero.title-bicolor :white_text="$text_item['white_text']" :yellow_text="$text_item['yellow_text']" :use_promo_font="$text_item['use_promo_font']" />
								@endif
							@endif

							@if ( 'subtitle' === $text_item['type'] && ! empty( $text_item['text'] ) )
								<x-search-hero.sub-title :title="$text_item['text']" :text_color="$text_item['color']" :use_promo_font="$text_item['use_promo_font']" />
							@endif

						@endforeach
					@endif

					@if ( 'search_bar' === $key )
						<x-search-hero.search-bar>
							{!! $item !!}
						</x-search-hero.search-bar>
					@endif

					@if ( 'thumbnail_cards' === $key )
						<x-search-hero.thumbnail-cards
							:items="$item['items'] ?? []"
							:hero_details_slider="$hero_details_slider"
						>
							{!! $item['slot'] !!}
						</x-search-hero.thumbnail-cards>
					@endif
				@endforeach
			</x-search-hero.left>
		@endif
		@if ( ! empty( $right ) )
			<x-search-hero.right>
				{!! $right !!}
			</x-search-hero.right>
		@endif
	</x-search-hero.content>
</x-search-hero>
