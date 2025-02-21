@props( [
	'cards' => [],
] )

@php
	$prev_ship_title = '';
@endphp

<x-section class="dates-rates__cards">
	@foreach ( $cards as $card )
		@php
			$ship_title = $card['ship_title'] ?? '';
			$number_of_promos = $card['available_promos'] ? count($card['available_promos']) : 0;
			$number_of_cabins = ! empty( $card['cabin_data'] ) ? count( $card['cabin_data'] ) : 0;
			$number_of_table_rows = $number_of_promos + 2; // 2 for brochure-price row and availability row.

			// Check if tax exists.
			if ( ! empty( $card['tax_types'] ) && is_array( $card['tax_types'] ) ) {
				$number_of_table_rows++;
			}

		@endphp

		@if ( $ship_title !== $prev_ship_title )
			<h2>
				<x-escape :content="$ship_title" />
			</h2>
			@php
				$prev_ship_title = $ship_title;
			@endphp
		@endif

		<x-dates-rates.item>
			<x-dates-rates.item.table title="Cabin Categories">
				{{-- Table Head --}}
				<x-dates-rates.item.table-head>
					<x-dates-rates.item.table-row>
						<x-dates-rates.item.table-heading>{{ __( 'Expedition', 'qrk' ) }}</x-dates-rates.item.table-heading>

						@if ( empty( $card['cabin_data'] ) )
							<x-dates-rates.item.table-heading colspan="2">
								{{ __( 'Cabins', 'qrk' ) }}
							</x-dates-rates.item.table-heading>
						@else
							<x-dates-rates.item.table-heading>{{ __( 'Promo Offers', 'qrk' ) }}</x-dates-rates.item.table-heading>

							{{-- Cabin Names --}}
							@foreach ( $card['cabin_data'] as $cabin )
								<x-dates-rates.item.table-heading :type="strtolower( $cabin['type'] )" >{{ $cabin['name'] ?? '' }}</x-dates-rates.item.table-heading>
							@endforeach
						@endif

					</x-dates-rates.item.table-row>
				</x-dates-rates.item.table-head>

				{{-- Table Body --}}
				<x-dates-rates.item.table-body>

					{{-- Expedition and cabin details with original price --}}
					<x-dates-rates.item.table-row>
						<x-dates-rates.item.table-column rowspan="{{ $number_of_table_rows }}">
							<x-dates-rates.expedition>
								<x-dates-rates.expedition.overline>
									<x-dates-rates.expedition.overline-link title="{{ $card['region'] ?? '' }}"
										url="" />
									<x-dates-rates.expedition.overline-link title="{{ $card['ship_title'] ?? '' }}"
										url="{{ $card['ship_link'] ?? '' }}" />
								</x-dates-rates.expedition.overline>
								<x-dates-rates.expedition.title text="{{ $card['expedition_title'] ?? '' }}"
									url="{{ $card['expedition_link'] ?? '' }}" />
								<x-dates-rates.expedition.dates duration_date="{{ $card['duration_dates'] ?? '' }}"
									duration="{{ $card['duration_days'] ?? 0 }}" />
								<x-dates-rates.expedition.meta>
									<x-dates-rates.expedition.meta-item>
										<x-dates-rates.expedition.meta-label>
											{{ __( 'Start Location', 'qrk' ) }}
										</x-dates-rates.expedition.meta-label>
										<x-dates-rates.expedition.meta-value>
											{{ $card['start_location'] ?? '' }}
										</x-dates-rates.expedition.meta-value>
									</x-dates-rates.expedition.meta-item>
									<x-dates-rates.expedition.meta-item>
										<x-dates-rates.expedition.meta-label>
											{{ __( 'End Location', 'qrk' ) }}
										</x-dates-rates.expedition.meta-label>
										<x-dates-rates.expedition.meta-value>
											{{ $card['end_location'] ?? '' }}
										</x-dates-rates.expedition.meta-value>
									</x-dates-rates.expedition.meta-item>
									<x-dates-rates.expedition.meta-item>
										<x-dates-rates.expedition.meta-label>
											{{ __( 'Languages', 'qrk' ) }}
										</x-dates-rates.expedition.meta-label>
										<x-dates-rates.expedition.meta-value>
											{{ $card['languages'] ?? '' }}
										</x-dates-rates.expedition.meta-value>
									</x-dates-rates.expedition.meta-item>
								</x-dates-rates.expedition.meta>
								@if ( ! empty( $card['request_a_quote_url'] ) )
									<x-dates-rates.expedition.cta :text="__( 'Request a Quote', 'qrk' )" :url="$card['request_a_quote_url']" />
								@endif
							</x-dates-rates.expedition>
						</x-dates-rates.item.table-column>

						@if ( ! empty( $card['cabin_data'] ) )
							<x-dates-rates.item.table-column>
								<x-dates-rates.item.table-column-title>
									<strong>{{ __( 'Brochure Price', 'qrk' ) }}</strong>

									@if ( !empty( $card['transfer_package_details'] ) && ! empty( $card['transfer_package_details']['title'] ) )
										{!! $card['transfer_package_details']['offer_inclusion_text'] ? $card['transfer_package_details']['offer_inclusion_text'] : __( 'Incl. Transfer Package', 'qrk' ) !!}
										<x-tooltip icon="info">
											<h5>{{ $card['transfer_package_details']['title'] }}</h5>

											@if ( !empty( $card['transfer_package_details']['sets'] ) )
												<ul>
													@foreach ( $card['transfer_package_details']['sets'] as $item )
														<li>
															{{ $item }}
														</li>
													@endforeach
												</ul>
											@endif

											<p>
												{{ __( 'Package Price:', 'qrk' ) }}
												{{ $card['transfer_package_details']['formatted_price'] ?? 0 }}
											</p>
										</x-tooltip>
									@endif

								</x-dates-rates.item.table-column-title>
							</x-dates-rates.item.table-column>

							{{-- Cabin Details --}}
							@foreach ( $card['cabin_data'] as $cabin )
								@php
									// Check if the cabin is sold out or has limited stock.
									$availability_status_code = ! empty( $cabin['availability_status'] )
										? $cabin['availability_status']
										: '';
									$is_sold_out = $availability_status_code === 'S';
								@endphp
								<x-dates-rates.item.table-column :is_sold_out="$is_sold_out">

									@if ( empty( $cabin['brochure_price'] ) )
										<x-dates-rates.item.dash-icon />
									@else
										@if ( empty($cabin['promos'] ) )
											{{ $cabin['brochure_price'] ?? 0 }}
										@else
											<del>
												{{ $cabin['brochure_price'] ?? 0 }}
											</del>
										@endif
									@endif

								</x-dates-rates.item.table-column>
							@endforeach
						@endif

					</x-dates-rates.item.table-row>

					{{-- Promo prices --}}
					@if ( !empty( $card['available_promos'] ) && ! empty( $card['cabin_data'] ) )
						@foreach ( $card['available_promos'] as $promo_code => $promo_data )
							@php
								$is_pay_in_full = empty( $promo_data['is_pif'] ) ? false : true;
								$is_discounted = ! $is_pay_in_full;
								$is_sold_out =
									$cabin['availability_status'] === 'S' || $cabin['availability_status'] === 'R';
							@endphp
							<x-dates-rates.item.table-row>
								<x-dates-rates.item.table-column :is_pay_in_full="$is_pay_in_full">
									<x-dates-rates.item.table-column-title>
										<x-dates-rates.item.promo-code
											drawer_id="{{ 'promo-code' . $promo_data['code'] }}"
											drawer_title="{!! $promo_data['description'] !!}"
											label="{!! $promo_data['description'] !!}"
											promo_code="{!! $promo_data['code'] !!}"
										/>
									</x-dates-rates.item.table-column-title>
								</x-dates-rates.item.table-column>

								{{-- Cabin-wise promo price --}}
								@foreach ( $card['cabin_data'] as $cabin )
									@php
										// Check if the cabin is sold out or has limited stock.
										$availability_status_code = ! empty( $cabin['availability_status'] )
											? $cabin['availability_status']
											: '';
										$is_sold_out = $availability_status_code === 'S';
									@endphp
									<x-dates-rates.item.table-column :is_pay_in_full="$is_pay_in_full" :is_sold_out="$is_sold_out" :is_discounted="$is_discounted">
										@if ( ! empty( $cabin['promos'][$promo_code] ) )
											@if ( empty( $promo_data['discount_value'] ) )
												<x-dates-rates.item.check-icon />
											@else
												{{ $cabin['promos'][$promo_code] }}
											@endif
										@else
											<x-dates-rates.item.dash-icon />
										@endif
									</x-dates-rates.item.table-column>
								@endforeach
							</x-dates-rates.item.table-row>
						@endforeach
					@endif

					@if ( empty( $card['cabin_data'] ) )
						<x-dates-rates.item.table-row>
							<x-dates-rates.item.table-column colspan="{{ $number_of_cabins + 2 }}">
								{{ __( 'No cabins available', 'qrk' ) }}
							</x-dates-rates.item.table-column>
						</x-dates-rates.item.table-row>
					@else
						{{-- Availability --}}
						<x-dates-rates.item.table-row>
							<x-dates-rates.item.table-column>
								<x-dates-rates.item.table-column-title>
									<strong>{{ __( 'Availability', 'qrk' ) }}</strong>
								</x-dates-rates.item.table-column-title>
							</x-dates-rates.item.table-column>

							{{-- Cabin-wise availability --}}
							@foreach ( $card['cabin_data'] as $cabin )
								@php
									// Initialize variables.
									$is_limited_stock = false;
									$availability_description = '';

									// Check if the cabin is sold out or has limited stock.
									$availability_status_code = ! empty( $cabin['availability_status'] )
										? $cabin['availability_status']
										: '';
									$is_sold_out = $availability_status_code === 'S';

									// Set availability description based on availability status.
									if ( $availability_status_code === 'A' ) {
										$spaces_available = $cabin['spaces_available'] ?? 0;

										if ( $spaces_available < 1 ) {
											// Cabin is sold out.
											$is_sold_out = true;
											$availability_description = __( 'Sold Out', 'qrk' );
										} elseif ( $spaces_available <= 5 ) {
											// Cabin has limited stock.
											$is_limited_stock = true;
											$availability_description = sprintf(
												'%d %s',
												$cabin['spaces_available'],
												_n( 'cabin', 'cabins', $cabin['spaces_available'], 'qrk' ),
											);
										} else {
											// Cabin has more than 5 cabins available.
											$availability_description = __( '5+ cabins', 'qrk' );
										}
									} else {
										// Cabin is not available.
										$availability_description = ! empty( $cabin['availability_description'] )
											? $cabin['availability_description']
											: '';
									}
								@endphp
								<x-dates-rates.item.table-column :is_stock_limited="$is_limited_stock" :is_sold_out="$is_sold_out">
									{{ $availability_description }}
								</x-dates-rates.item.table-column>
							@endforeach
						</x-dates-rates.item.table-row>
					@endif

					{{-- Tax types --}}
					@if ( ! empty( $card['tax_types'] ) && is_array( $card['tax_types'] ) )
						@foreach ( $card['tax_types'] as $tax_type )
							@if ( ! empty( $tax_type ) && is_array( $tax_type ) && ! empty( $tax_type['rate'] ) )
								<x-dates-rates.item.table-row>
									<x-dates-rates.item.table-column>
										<x-dates-rates.item.table-column-title>
											<x-dates-rates.item.gst-rate :rate="$tax_type['rate']" />
										</x-dates-rates.item.table-column-title>
									</x-dates-rates.item.table-column>

									@for ( $i = $number_of_cabins; $i > 0; $i-- )
										<x-dates-rates.item.table-column>
											<x-dates-rates.item.check-icon />
										</x-dates-rates.item.table-column>
									@endfor

								</x-dates-rates.item.table-row>
							@endif
						@endforeach
					@endif

				</x-dates-rates.item.table-body>

				{{-- Table Foot --}}
				<x-dates-rates.item.table-foot>
					<x-dates-rates.item.table-row>
						<x-dates-rates.item.table-column colspan="{{ $number_of_cabins + 2 }}">
							<x-dates-rates.adventure-options>

								@if ( ! empty( $card['included_adventure_options'] ) )
									<x-dates-rates.adventure-options.column :title="__( 'Included Adventure Options' ,'qrk' )">

										@foreach ( $card['included_adventure_options'] as $included_adventure_option )
											<x-dates-rates.adventure-options.item
												name="{{ $included_adventure_option['title'] ?? '' }}"
												icon="{{ $included_adventure_option['icon_image_id'] }}"
											/>
										@endforeach

									</x-dates-rates.adventure-options.column>
								@endif

								@if ( $card['paid_adventure_options'] )
									<x-dates-rates.adventure-options.column title="Paid Adventure Options">

										@foreach ( $card['paid_adventure_options'] as $paid_adventure_option )
											<x-dates-rates.adventure-options.item
												name="{{ $paid_adventure_option['title'] ?? '' }}"
												icon="{{ $paid_adventure_option['icon_image_id'] }}" :is_paid="true">
												<x-dates-rates.adventure-options.item-price
													price="{{ $paid_adventure_option['price_per_person'] ?? '' }}"
													currency="{{ $paid_adventure_option['currency'] ?? '' }}"
													count="{{ $paid_adventure_option['spaces_available'] ?? 0 }}" />
											</x-dates-rates.adventure-options.item>
										@endforeach

									</x-dates-rates.adventure-options.column>
								@endif

							</x-dates-rates.adventure-options>
						</x-dates-rates.item.table-column>
					</x-dates-rates.item.table-row>
				</x-dates-rates.item.table-foot>

			</x-dates-rates.item.table>

			<x-dates-rates.item.info :text="__( 'Prices are shown per person', 'qrk' )" />

		</x-dates-rates.item>

	@endforeach
</x-section>
