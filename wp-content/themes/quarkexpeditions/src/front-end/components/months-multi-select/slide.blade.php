@props( [
	'years'      => [],
	'is_compact' => false,
] )

@php
	if ( empty( $years ) ) {
		return;
	}

	$month_options = [ 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec' ];

	$month_name_mapping = [
		'Jan'  => __( 'January', 'qrk' ),
		'Feb'  => __( 'February', 'qrk' ),
		'Mar'  => __( 'March', 'qrk' ),
		'Apr'  => __( 'April', 'qrk' ),
		'May'  => __( 'May', 'qrk' ),
		'Jun'  => __( 'June', 'qrk' ),
		'Jul'  => __( 'July', 'qrk' ),
		'Aug'  => __( 'Aug', 'qrk' ),
		'Sept' => __( 'September', 'qrk' ),
		'Oct'  => __( 'October', 'qrk' ),
		'Nov'  => __( 'November', 'qrk' ),
		'Dec'  => __( 'December', 'qrk' ),
	];
@endphp

{{-- TODO: Resolve issue with duplicate slide markup. --}}
{{-- Slide Content for Mobile Devices --}}
@if ( ! empty(  $is_compact  ) )
	@foreach ( $years as $year )
		<tp-slider-slide class="months-multi-select__slide months-multi-select__slide--compact">
			@if ( ! empty( $year ) )
				<p class="months-multi-select__slide-title overline"><x-escape :content="$year"/></p>
			@endif
			<div class="months-multi-select__slide-content">
				<quark-months-multi-select-options class="months-multi-select__options">
					@if ( ! empty( $month_options ) )
						@foreach ( $month_options as $index => $month )
							@php
								$option_item_value = '';
								$is_past_date      = false;

								if ( $index + 1 < 10 ) {
									$option_item_value = sprintf( "0%d-%d", $index + 1, $year );
								} else {
									$option_item_value = sprintf( "%d-%d", $index + 1, $year );
								}

								// Get current month-year.
								$current_date = DateTime::createFromFormat( 'm-Y', date( 'm-Y' ) );
								$option_value = DateTime::createFromFormat( 'm-Y', $option_item_value );

								$is_past_date = $current_date > $option_value ? true : false;
							@endphp
							<quark-months-multi-select-option
								class="months-multi-select__options-item"
								role="button"
								disabled="{{ $is_past_date ? 'yes' : 'no' }}"
								selected="no"
								label="{{ sprintf( '%s, %s', $month_name_mapping[ $month ] ?? '', $year ) }}"
								value="{{ $option_item_value ?? '' }}"
							>
								{{ $month }}
							</quark-months-multi-select-option>
						@endforeach
					@endif
				</quark-months-multi-select-options>
			</div>
		</tp-slider-slide>
	@endforeach
@else
	<tp-slider-slide class="months-multi-select__slide">
		@foreach ( $years as $year )
			<div class="months-multi-select__year">
				@if ( ! empty( $year ) )
					<p class="months-multi-select__slide-title overline"><x-escape :content="$year"/></p>
				@endif
				<div class="months-multi-select__slide-content">
					<quark-months-multi-select-options class="months-multi-select__options">
						@if ( ! empty( $month_options ) )
							@foreach ( $month_options as $index => $month )
								@php
									$option_item_value = '';
									$is_past_date = false;

									if ( $index + 1 < 10 ) {
										$option_item_value = sprintf( "0%d-%d", $index + 1, $year );
									} else {
										$option_item_value = sprintf( "%d-%d", $index + 1, $year );
									}

									// Get current month-year.
									$current_date = DateTime::createFromFormat( 'm-Y', date( 'm-Y' ) );
									$option_value = DateTime::createFromFormat( 'm-Y', $option_item_value );

									// Check if is past date.
									$is_past_date = $current_date > $option_value ? true : false;
								@endphp
								<quark-months-multi-select-option
									class="months-multi-select__options-item"
									role="button"
									disabled="{{ $is_past_date ? 'yes' : 'no' }}"
									selected="no"
									label="{{ sprintf( '%s, %s', $month_name_mapping[ $month ] ?? '', $year ) }}"
									value="{{ $option_item_value ?? '' }}"
								>
									{{ $month }}
								</quark-months-multi-select-option>
							@endforeach
						@endif
					</quark-months-multi-select-options>
				</div>
			</div>
		@endforeach
	</tp-slider-slide>
@endif
