@props( [
	'years' => [],
] )

@php
	if ( empty( $years ) ) {
		return;
	}

	$month_options = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' ];
@endphp

<tp-slider-slide class="months-multi-select__slide">
	<x-two-columns :border="true">
		@foreach ( $years as $year )
			<x-two-columns.column>
				@if ( ! empty( $year ) )
					<p class="months-multi-select__slide-title overline"><x-escape :content="$year"/></p>
				@endif
				<div class="months-multi-select__slide-content">
					<ul class="months-multi-select__month-options">
						@if ( ! empty( $month_options ) )
							@foreach ( $month_options as $month )
							// TODO: Add month-year value.
								<li class="months-multi-select__month-options-item" role="button" selected="false">
									{{ $month }}
								</li>
							@endforeach
						@endif
					</ul>
				</div>
			</x-two-columns.column>
		@endforeach
	</x-two-columns>
</tp-slider-slide>

{{-- Slide Content for Mobile Devices --}}
@foreach ( $years as $year )
	<tp-slider-slide class="months-multi-select__slide months-multi-select__slide--compact">
		@if ( ! empty( $year ) )
			<p class="months-multi-select__slide-title overline"><x-escape :content="$year"/></p>
		@endif
		<div class="months-multi-select__slide-content">
			<ul class="months-multi-select__month-options">
				@if ( ! empty( $month_options ) )
					@foreach ( $month_options as $month )
					// TODO: Add month-year value.
						<li class="months-multi-select__month-options-item" role="button" selected="false">
							{{ $month }}
						</li>
					@endforeach
				@endif
			</ul>
		</div>
	</tp-slider-slide>
@endforeach
