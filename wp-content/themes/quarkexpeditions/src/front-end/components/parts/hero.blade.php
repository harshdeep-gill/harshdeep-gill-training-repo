@props( [
	'image_id'        => 0,
	'immersive'       => 'no',
	'content_overlap' => true,
	'text_align'      => '',
	'overlay_opacity' => 0,
	'left'            => [],
	'right'           => [],
	'dark_mode'       => false,
	'breadcrumbs'     => '',
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<x-hero :immersive="$immersive" :text_align="$text_align" :overlay_opacity="$overlay_opacity" :content_overlap="$content_overlap">
	<x-hero.image :image_id="$image_id" />
	{!! $breadcrumbs !!}
	<x-hero.content>
		<x-hero.left>
			<x-hero.title-container>
				@foreach ( $left as $item )
					@if (  'overline' === $item['type'] )
						@if ( ! empty( $item['overline']['text'] ) )
							<x-hero.overline :color="$item['overline']['color']">
								{!! $item['overline']['text'] !!}
							</x-hero.overline>
						@endif
					@endif

					@if ( 'title' === $item['type'] )
						@if ( ! empty( $item['title'] ) )
							<x-hero.title :title="$item['title']" :text_color="$item['text_color']" :use_promo_font="$item['use_promo_font']" />
						@endif
					@endif

					@if ( 'title_bicolor' === $item['type'] )
						@if ( ! empty( $item['white_text'] && ! empty( $item['yellow_text'] ) ) )
							<x-hero.title-bicolor :white_text="$item['white_text']" :yellow_text="$item['yellow_text']" :switch_colors="$item['switch_colors']" :use_promo_font="$item['use_promo_font']" />
						@endif
					@endif

					@if ( 'text-graphic' === $item['type'] )
						@if ( ! empty( $item['image_id'] ) )
							<x-hero.text-graphic :image_id="$item['image_id']" />
						@endif
					@endif

					@if ( 'subtitle' === $item['type'] )
						@if ( ! empty( $item['subtitle'] ) )
							<x-hero.sub-title :title="$item['subtitle']" :text_color="$item['text_color']" :use_promo_font="$item['use_promo_font']" />
						@endif
					@endif

					@if ( 'description' === $item['type'] )
						@if ( ! empty( $item['description'] ) )
							<x-hero.description :text_color="$item['text_color']" :use_promo_font="$item['use_promo_font']">
								{!! $item['description'] !!}
							</x-hero.description>
						@endif
					@endif
				@endforeach
			</x-hero.title-container>

			@foreach ( $left as $item )
				@if( 'tag' === $item['type'])
					@if ( ! empty( $item['tag'] ) )
						{!! $item['tag'] !!}
					@endif
				@endif

				@if( 'cta' === $item['type'])
					@if ( ! empty( $item['cta'] ) )
						{!! $item['cta'] !!}
					@endif
				@endif

				@if( 'button' === $item['type'])
					@if ( ! empty( $item['button'] ) )
						{!! $item['button'] !!}
					@endif
				@endif
			@endforeach
		</x-hero.left>
		<x-hero.right>
			@foreach ( $right as $item )
				@if ( 'form' === $item['type'] )
					@if ( ! empty( $item['form'] ) )
						<x-hero.form>
							{!! $item['form'] !!}
						</x-hero.form>
					@endif
				@endif

				@if ( 'circle-badge' === $item['type'] )
					<x-hero.circle-badge :text="$item['text']" />
				@endif
			@endforeach
		</x-hero.right>
	</x-hero.content>
</x-hero>
