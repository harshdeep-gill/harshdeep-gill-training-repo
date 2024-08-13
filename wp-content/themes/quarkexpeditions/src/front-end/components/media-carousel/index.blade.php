@props( [
	'class' => '',
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	$classes = [ 'media-carousel' ];

	if ( ! empty( $class ) ) {
		$classes[] = $class;
	}

	$slide_count = quark_get_slot_child_count( $slot );
@endphp

<tp-slider
	@class( $classes )
	swipe="yes"
	behaviour="fade"
	infinite="yes"
>
	<tp-slider-track class="media-carousel__track">
		<tp-slider-slides class="media-carousel__slides">
			{!! $slot !!}
		</tp-slider-slides>

		<tp-slider-nav>
			@for ($i = 0; $i < $slide_count; $i++)
				<tp-slider-nav-item><button>{{ $i }}</button></tp-slider-nav-item>
			@endfor
		</tp-slider-nav>
	</tp-slider-track>

	@if ( $slide_count > 1 )
		<div class="media-carousel__arrows">
			<tp-slider-arrow direction="previous">
				<button class="media-carousel__arrow-button media-carousel__arrow-button--left">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>
			<tp-slider-arrow direction="next">
				<button class="media-carousel__arrow-button media-carousel__arrow-button--right">
					<x-svg name="chevron-left" />
				</button>
			</tp-slider-arrow>
		</div>
	@endif
</tp-slider>
