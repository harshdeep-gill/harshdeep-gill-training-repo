@props( [
	'reset_button_text' => __( "I'm Flexible", 'qrk' ),
	'button_text' => '',
	'is_compact'  => false,
] )

@php
	if ( empty( $slot ) ) {
		return;
	}

	// Get slide count.
	$slide_count = quark_get_slot_child_count( $slot );

	$classes = [ 'months-multi-select__slider' ];

	if ( ! empty( $is_compact ) ) {
		$classes[] = 'months-multi-select__slider--compact';
	}
@endphp

<tp-slider @class( $classes ) swipe="yes" infinite="yes">
	<tp-slider-track class="months-multi-select__track">
		@if ( ! empty( $reset_button_text ) )
			<x-button
				class="months-multi-select__reset-button months-multi-select__reset-button-mobile"
				appearance="outline"
				color="black"
				active
			>
				<x-escape :content="$reset_button_text" />
			</x-button>
		@endif
		<tp-slider-slides>
			{!! $slot !!}
		</tp-slider-slides>
	</tp-slider-track>

	<div class="months-multi-select__nav">
		@if ( ! empty( $reset_button_text ) )
		<div class="months-multi-select__reset">
			<button class="months-multi-select__reset-button" active>
				<x-escape :content="$reset_button_text" />
			</button>
			<x-svg name="check-circle" />
		</div>
		@endif

		@if ( $slide_count > 1 )
			<div class="months-multi-select__nav-arrows">
				<tp-slider-arrow direction="previous">
					<button class="months-multi-select__arrow-button months-multi-select__arrow-button--left">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>

				<tp-slider-arrow direction="next">
					<button class="months-multi-select__arrow-button months-multi-select__arrow-button--right">
						<x-svg name="chevron-left" />
					</button>
				</tp-slider-arrow>
			</div>
		@endif
	</div>
</tp-slider>
