@props( [
	'interval'        => 5,
	'show_controls'   => true,
	'transition_type' => false,
	'items'           => [],
] )

@php
	if ( empty( $items ) ) {
		return;
	}
@endphp

<x-hero-card-slider :arrows="$show_controls" :interval="$interval" :auto_slide="'auto' === $transition_type">
	@foreach ( $items as $item )
		<x-hero-card-slider.card>
			@if ( ! empty( $item['media_id'] ) && 'video' === $item['media_type'] )
				<x-hero-card-slider.video video_id="{{ $item['media_id'] }}" />
			@endif

			@if ( ! empty( $item['media_id'] ) && 'image' === $item['media_type'] )
				<x-hero-card-slider.image image_id="{{ $item['media_id'] }}" />
			@endif

			<x-hero-card-slider.content>
				@if ( ! empty( $item['tag'] ) )
					@if ( 'overline' === $item['tag']['type'] )
						<x-hero-card-slider.overline :text="$item['tag']['text']" />
					@elseif ( 'tag' === $item['tag']['type'] )
						<x-hero-card-slider.tag :text="$item['tag']['text']" />
					@endif
				@endif
				@if ( ! empty( $item['title'] ) )
					<x-hero-card-slider.title :title="$item['title']" />
				@endif

				@if ( ! empty( $item['description'] ) )
					<x-hero-card-slider.description>
						{!! $item['description'] !!}
					</x-hero-card-slider.description>
				@endif

				@if ( ! empty( $item['cta'] ) )
					<x-hero-card-slider.card-cta :text="$item['cta']['text']" :url="$item['cta']['url']" />
				@endif
			</x-hero-card-slider.content>
		</x-hero-card-slider.card>
	@endforeach
</x-hero-card-slider>
