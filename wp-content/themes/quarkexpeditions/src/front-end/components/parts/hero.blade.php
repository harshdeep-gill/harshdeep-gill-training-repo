@props( [
	'image_id'        => 0,
	'immersive'       => false,
	'text_align'      => '',
	'overlay_opacity' => 0,
	'left'            => [],
	'right'           => [],
	'dark_mode'       => false,
] )

@php
	if ( empty( $image_id ) ) {
		return;
	}
@endphp

<x-hero :immersive="$immersive" :text_align="$text_align" :overlay_opacity="$overlay_opacity">
	<x-hero.image :image_id="$image_id" />
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
							<x-hero.title :title="$item['title']" />
						@endif
					@endif

					@if ( 'subtitle' === $item['type'] )
						@if ( ! empty( $item['subtitle'] ) )
							<x-hero.sub-title :title="$item['subtitle']" />
						@endif
					@endif

					@if ( 'description' === $item['type'] )
						@if ( ! empty( $item['description'] ) )
							<x-hero.description :text_color="$item['text_color']">
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
				@if( 'form' === $item['type'] )
					@if ( ! empty( $item['form'] ) )
						<x-hero.form>
							{!! $item['form'] !!}
						</x-hero.form>
					@endif
				@endif
			@endforeach
		</x-hero.right>
	</x-hero.content>
</x-hero>
