@props( [
	'cards' => [],
] )

<x-departure-cards>
	@foreach( $cards as $card )
		@php
			$departure_id = $card['departure_id'] ?? uniqid();
		@endphp

		<x-button size="big" appearance="outline" href="tel:+55555555" class="dynamic-phone-number__link dynamic-phone-number-and-prefix">{{ __( 'Request a callback:', 'qrk' ) }} 555555</x-button>
		<x-departure-cards.card>
			<x-departure-cards.card-banner text="{{ $card['banner_details']['title'] ?? '' }}"/>
			<x-departure-cards.header>
				<x-departure-cards.title title="{{ $card['expedition_name'] ?? '' }}"/>
				@if( ! empty( $card['promotion_banner'] ) )
					<x-departure-cards.promo-tag text="{{ $card['promotion_banner'] }}"/>
				@endif
			</x-departure-cards.header>
			<x-departure-cards.body>
				<x-departure-cards.body-column>
					<x-departure-cards.specifications>
						@if ( ! empty( $card['duration_days'] ) || ! empty( $card['duration_dates'] ) )
							<x-departure-cards.specification-item>
								<x-departure-cards.specification-label>
									<x-escape :content="__( 'Itinerary', 'qrk' )"/>
								</x-departure-cards.specification-label>
								<x-departure-cards.specification-value>
									@if ( ! empty( $card['duration_days'] ) )
										@php
											$duration = sprintf( __( '%d Days', 'qrk' ), $card['duration_days'] );
										@endphp
										<x-escape :content="$duration"/><br>
									@endif

									@if ( ! empty( $card['duration_dates'] ) )
										{{ $card['duration_dates'] }}
									@endif
								</x-departure-cards.specification-value>
							</x-departure-cards.specification-item>
						@endif

						@if ( ! empty( $card['starting_from_location'] ) )
							<x-departure-cards.specification-item>
								<x-departure-cards.specification-label>
									<x-escape :content="__( 'Starting from', 'qrk' )"/>
								</x-departure-cards.specification-label>
								<x-departure-cards.specification-value>
									{{ $card['starting_from_location'] }}
								</x-departure-cards.specification-value>
							</x-departure-cards.specification-item>
						@endif

						@if ( ! empty( $card['ship_name'] ) )
							<x-departure-cards.specification-item>
								<x-departure-cards.specification-label>
									<x-escape :content="__( 'Ship', 'qrk' )"/>
								</x-departure-cards.specification-label>
								<x-departure-cards.specification-value>
									{{ $card['ship_name'] }}
								</x-departure-cards.specification-value>
							</x-departure-cards.specification-item>
						@endif

						@if ( ! empty( $card['languages'] ) )
							<x-departure-cards.specification-item>
								<x-departure-cards.specification-label>
									<x-escape :content="__( 'Languages', 'qrk' )"/>
								</x-departure-cards.specification-label>
								<x-departure-cards.specification-value>
									{{ $card['languages'] }}
								</x-departure-cards.specification-value>
							</x-departure-cards.specification-item>
						@endif

						@if ( ! empty( $card['paid_adventure_options'] ) )
							<x-departure-cards.specification-item>
								<x-departure-cards.specification-label>
									<x-escape :content="__( 'Adventure Options', 'qrk' )"/>
								</x-departure-cards.specification-label>
								<x-departure-cards.specification-value>
									<x-departure-cards.adventure-options>
										@foreach( $card['paid_adventure_options'] as $option )
											<x-departure-cards.adventure-option title="{{ $option }}"/>
										@endforeach

										<x-departure-cards.adventure-options-tooltip>
											<ul>
												@foreach( $card['paid_adventure_options'] as $option )
													<li>{{ $option }}</li>
												@endforeach
											</ul>
										</x-departure-cards.adventure-options-tooltip>
									</x-departure-cards.adventure-options>
								</x-departure-cards.specification-value>
							</x-departure-cards.specification-item>
						@endif
					</x-departure-cards.specifications>

					@if ( ! empty( $card['promotion_tags'] ) )
						<x-departure-cards.offers :title="__( 'Available Offers', 'qrk' )">
							@foreach( $card['promotion_tags'] as $offer )
								<x-departure-cards.offer title="{{ $offer }}"/>
							@endforeach

							<x-departure-cards.offers-modal title="{{ $card['expedition_name'] ?? '' }}">
								<ul>
									@foreach( $card['promotion_tags'] as $offer )
										<li>{{ $offer }}</li>
									@endforeach
								</ul>
							</x-departure-cards.offers-modal>
						</x-departure-cards.offers>
					@endif
				</x-departure-cards.body-column>

				<x-departure-cards.body-column>
					<x-departure-cards.price
						original_price="{{ $card['lowest_price']['original_price'] ?? '' }}"
						discounted_price="{{ $card['lowest_price']['discounted_price'] ?? '' }}"
					/>

					@if( ! empty( $card['transfer_package_details'] && ! empty( $card['transfer_package_details']['sets'] ) ) )
						<x-departure-cards.transfer_package
							drawer_id="{{ $departure_id }}"
							drawer_title="Mandatory Transfer Package"
						>
							<p><strong>{{ $card['transfer_package_details']['title'] ?? '' }}</strong></p>
							<ul>
								@foreach( $card['transfer_package_details']['sets'] as $set )
									<li>{!! $set !!}</li>
								@endforeach
							</ul>
							<p><strong>Package
									Price: {{ $card['transfer_package_details']['formatted_price'] ?? '' }}</strong></p>
						</x-departure-cards.transfer_package>
					@endif
					<x-departure-cards.cta text="View Cabin Pricing & Options"/>
				</x-departure-cards.body-column>
			</x-departure-cards.body>

			@if( ! empty( $card['cabins'] ) && is_array( $card['cabins'] ) ) @endif
			<x-departure-cards.more-details>
				<h4>
					<x-escape :content="__( 'Cabins Options', 'qrk' )"/>
				</h4>
				<x-product-options-cards>
					<x-product-options-cards.cards>
						@foreach( $card['cabins'] as $cabin_code => $cabin )
							@php
								$cabin_availability_status = $cabin['specifications']['availability_status'] ?? 'U';

								if ( empty( $cabin_availability_status ) || 'U' === $cabin_availability_status ) {
									continue;
								}
							@endphp
							<x-product-options-cards.card details_id="{{  $cabin_code . '_' . $departure_id }}" :status="$cabin_availability_status" >

								@if( ! empty( $cabin['gallery'] ) )
									<x-product-options-cards.gallery :image_ids="$cabin['gallery']">
										<x-product-options-cards.badge
											status="{{ $cabin_availability_status }}"
											type="{{ $cabin['type'] }}"
										/>
									</x-product-options-cards.gallery>
								@endif

								<x-product-options-cards.content>
									<x-product-options-cards.title title="{{ $cabin['name'] ?? '' }}"/>

									@if( ! empty( $cabin['specifications'] ) && is_array( $cabin['specifications'] ) )
										<x-product-options-cards.specifications>
											@if( ! empty( $cabin['specifications']['occupancy'] ) )
												<x-product-options-cards.specification
													label="Occupancy"
													value="{{ $cabin['specifications']['occupancy'] }}"
												/>
											@endif

											@if( ! empty( $cabin['specifications']['bed_configuration'] ) )
												<x-product-options-cards.specification
													label="Number of Beds"
													value="{{ $cabin['specifications']['bed_configuration'] }}"
												/>
											@endif

											@if( ! empty( $cabin['specifications']['location'] ) )
												<x-product-options-cards.specification
													label="Location"
													value="{{ $cabin['specifications']['location'] }}"
												/>
											@endif

											@if( ! empty( $cabin['specifications']['size'] ) )
												<x-product-options-cards.specification
													label="Cabin Size"
													value="{{ $cabin['specifications']['size'] }}"
												/>
											@endif
										</x-product-options-cards.specifications>
									@endif

									@if( ! empty( $cabin['from_price'] ) && is_array( $cabin['from_price'] ) )
										<x-product-options-cards.price
											original_price="{{ $cabin['from_price']['original_price'] ?? '' }}"
											discounted_price="{{ $cabin['from_price']['discounted_price'] ?? '' }}"
										/>
									@endif
								</x-product-options-cards.content>
							</x-product-options-cards.card>
						@endforeach
					</x-product-options-cards.cards>

					<x-product-options-cards.more-details>
						@foreach( $card['cabins'] as $cabin_code => $cabin )
							@php
								$cabin_availability_status = $cabin['specifications']['availability_status'] ?? 'U';

								if ( empty( $cabin_availability_status ) || in_array( $cabin_availability_status, [ 'U', 'R', 'S' ] ) ) {
									continue;
								}
							@endphp
							<x-product-options-cards.card-details
								id="{{ $cabin_code . '_' . $departure_id }}">

								@if( $cabin['name'] )
									<x-product-options-cards.card-details-title title="{{ $cabin['name'] }}"/>
								@endif

								@if( ! empty( $cabin['description'] ) )
									<x-product-options-cards.description>
										{!! $cabin['description'] !!}
									</x-product-options-cards.description>
								@endif

								@if( ! empty( $cabin['gallery'] ) )
									<x-product-options-cards.gallery :image_ids="$cabin['gallery']" :full_size="true"/>
								@endif

								@if( ! empty( $cabin['occupancies'] ) && is_array( $cabin['occupancies'] ) )
									<x-product-options-cards.rooms title="Select Rooms">
										@foreach( $cabin['occupancies'] as $occupancy )
											<x-product-options-cards.room>
												<x-product-options-cards.room-title-container>
													@if( ! empty( $occupancy['description'] ) )
														<x-product-options-cards.room-title
															title="{{ $occupancy['description'] }}"
															no_of_guests="{{ $occupancy['no_of_guests'] ?? '' }}"
														/>
													@endif
													<x-product-options-cards.room-subtitle subtitle="Price of the cabin for one guest"/>
												</x-product-options-cards.room-title-container>

												@if( ! empty( $occupancy['price'] ) && is_array( $occupancy['price'] ) )
													<x-product-options-cards.room-prices
														original_price="{{ $occupancy['price']['original_price'] }}"
														discounted_price="{{ $occupancy['price']['discounted_price'] }}"
													/>
												@endif
											</x-product-options-cards.room>
										@endforeach
									</x-product-options-cards.rooms>
								@endif

								@if( ! empty( $card['promotions'] ) && is_array( $card['promotions'] ) )
									<x-product-options-cards.discounts>
										@foreach( $card['promotions'] as $promotion )
											@if( ! empty( $promotion ) )
												<x-product-options-cards.discount name="{{ $promotion }}"/>
											@endif
										@endforeach
									</x-product-options-cards.discounts>
								@endif

								<x-product-options-cards.cta-buttons>
									<p class="product-options-cards__help-text">{{ __( 'Not ready to book?', 'qrk' ) }} <a href="#">{{ __( 'Request a quote', 'qrk' ) }}</a></p>
									<x-product-options-cards.phone-number phone_number="+1 (877) 585-1235" text="Request a callback: +1 (866) 257-0754" />
									<x-product-options-cards.cta-book-now url="#" />
								</x-product-options-cards.cta-buttons>
							</x-product-options-cards.card-details>
						@endforeach
					</x-product-options-cards.more-details>
				</x-product-options-cards>
			</x-departure-cards.more-details>
		</x-departure-cards.card>
	@endforeach
</x-departure-cards>
