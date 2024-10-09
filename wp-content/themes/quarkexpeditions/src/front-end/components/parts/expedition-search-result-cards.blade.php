@props( [
	'cards' => []
] )

@php
	if ( empty( $cards ) ) {
		return;
	}
@endphp

<x-expedition-cards>
	@foreach ( $cards as $card)
		<x-expedition-cards.card>
			<x-expedition-cards.card-banner text="Quark Protection Promise" url="#" />

			<x-expedition-cards.grid>
				<x-expedition-cards.grid-column>
					<x-expedition-cards.promo-tag text="{{ $card['promotion_banner'] ?? '' }}" />
					<x-expedition-cards.date>{{ $card['duration_dates'] ?? '' }}</x-expedition-cards.date>
					<x-expedition-cards.title>{{ $card['expedition_name'] ?? '' }}</x-expedition-cards.title>

					<x-expedition-cards.icons>
						<x-expedition-cards.icon icon="ship">{{ $card['ship_name'] ?? '' }}</x-expedition-cards.icon>
						<x-expedition-cards.icon icon="fly-express">
							Fly/Cruise Express
							<x-expedition-cards.tooltip title="Fly/Cruise Express Edition">
								<p>Spend less time traveling on Antarctic Peninsular Expeditions</p>
							</x-expedition-cards.tooltip>
						</x-expedition-cards.icon>
					</x-expedition-cards.icons>

					<x-media-carousel>
						<x-media-carousel.item image_id="29" />
						<x-media-carousel.item image_id="32" />
						<x-media-carousel.item image_id="152" />
					</x-media-carousel>
				</x-expedition-cards.grid-column>

				<x-expedition-cards.grid-column>
					<x-expedition-cards.specifications>
						<x-expedition-cards.specification-item>
							<x-expedition-cards.specification-label>
								{{ __( 'Itinerary', 'qrk' ) }}
							</x-expedition-cards.specification-label>
							<x-expedition-cards.specification-value>
								@if ( ! empty( $card['duration_days'] ) )
									@php
										$duration = sprintf( __( '%d Days', 'qrk' ), $card['duration_days'] );
										$duration_dates = sprintf( __( '(%s)', 'qrk' ), $card['duration_dates'] );
									@endphp
								@endif
								<x-escape :content="$duration"/> <br> <x-escape :content="$duration_dates"/>
							</x-expedition-cards.specification-value>
						</x-expedition-cards.specification-item>

						<x-expedition-cards.specification-item>
							<x-expedition-cards.specification-label>
								{{ __( 'Starting from', 'qrk' ) }}
							</x-expedition-cards.specification-label>
							<x-expedition-cards.specification-value>
								{{ $card['starting_from_location'] ?? '' }}
							</x-expedition-cards.specification-value>
						</x-expedition-cards.specification-item>

						<x-expedition-cards.specification-item>
							<x-expedition-cards.specification-label>
								{{ __( 'Languages', 'qrk' ) }}
							</x-expedition-cards.specification-label>
							<x-expedition-cards.specification-value>
								{{ $card['languages'] }}
							</x-expedition-cards.specification-value>
						</x-expedition-cards.specification-item>

						<x-expedition-cards.specification-item>
							<x-expedition-cards.specification-label>
								{{ __( 'Adventure Options', 'qrk' ) }}
							</x-expedition-cards.specification-label>
							<x-expedition-cards.specification-value>
								<x-expedition-cards.adventure-options>
									@foreach( $card['paid_adventure_options'] as $option )
										<x-expedition-cards.adventure-option title="{{ $option }}"/>
									@endforeach

									<x-expedition-cards.adventure-options-tooltip>
										<ul>
											@foreach( $card['paid_adventure_options'] as $option )
												<li>{{ $option }}</li>
											@endforeach
										</ul>
									</x-expedition-cards.adventure-options-tooltip>
								</x-expedition-cards.adventure-options>
							</x-expedition-cards.specification-value>
						</x-expedition-cards.specification-item>
					</x-expedition-cards.specifications>

					<x-expedition-cards.rating rating="5">
						<a href="#">45 Reviews</a>
					</x-expedition-cards.rating>

					<x-expedition-cards.price
						original_price="{{ $card['lowest_price']['original_price'] ?? '' }}"
						discounted_price="{{ $card['lowest_price']['discounted_price'] ?? '' }}"
					/>

					@if( ! empty( $card['transfer_package_details'] && ! empty( $card['transfer_package_details']['sets'] ) ) )
						<x-expedition-cards.transfer_package
							drawer_id="{{ 'drawer' . $departure_id }}"
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
						</x-expedition-cards.transfer_package>
					@endif

					<x-expedition-cards.buttons>
						<x-button href="#" color="black" size="big">View Expedition</x-button>
						<x-expedition-cards.cta text="View Cabin Pricing & Options" />
					</x-expedition-cards.buttons>
				</x-expedition-cards.grid-column>
			</x-expedition-cards.grid>

			<x-expedition-cards.more-details>
				<h4>Cabins Options</h4>
				<x-product-options-cards>
					<x-product-options-cards.cards>
						<x-product-options-cards.card>
							<x-product-options-cards.gallery :image_ids="[ 32, 34, 36]">
								<x-product-options-cards.badge type="standard" />
							</x-product-options-cards.gallery>
							<x-product-options-cards.content>
								<x-product-options-cards.title title="Explorer Suite" />
								<x-product-options-cards.specifications>
									<x-product-options-cards.specification
										label="Occupancy"
										value="1-2 guests"
									/>
									<x-product-options-cards.specification
										label="Number of Beds"
										value="1 double or 2 single beds"
									/>
									<x-product-options-cards.specification
										label="Location"
										value="Deck 3"
									/>
									<x-product-options-cards.specification
										label="Cabin Size"
										value="226 sq. ft."
									/>
								</x-product-options-cards.specifications>
								<x-product-options-cards.price
									original_price="$9,395 USD"
									discounted_price="$7,271 USD"
								/>
							</x-product-options-cards.content>
						</x-product-options-cards.card>
						<x-product-options-cards.card status="A" details_id="some-random-id-2">
							<x-product-options-cards.gallery :image_ids="[ 32, 34, 36]">
								<x-product-options-cards.badge type="premium" status="A" />
							</x-product-options-cards.gallery>
							<x-product-options-cards.content>
								<x-product-options-cards.title title="Explorer Suite" />
								<x-product-options-cards.specifications>
									<x-product-options-cards.specification
										label="Occupancy"
										value="1-2 guests"
									/>
									<x-product-options-cards.specification
										label="Number of Beds"
										value="1 double or 2 single beds"
									/>
									<x-product-options-cards.specification
										label="Location"
										value="Deck 3"
									/>
									<x-product-options-cards.specification
										label="Cabin Size"
										value="226 sq. ft."
									/>
								</x-product-options-cards.specifications>
								<x-product-options-cards.price
									original_price="$9,395 USD"
									discounted_price="$7,271 USD"
								/>
							</x-product-options-cards.content>
						</x-product-options-cards.card>
						<x-product-options-cards.card status="S" details_id="some-random-id-3">
							<x-product-options-cards.gallery :image_ids="[ 32, 34, 36]">
								<x-product-options-cards.badge status="S" />
							</x-product-options-cards.gallery>
							<x-product-options-cards.content>
								<x-product-options-cards.title title="Explorer Suite" />
								<x-product-options-cards.specifications>
									<x-product-options-cards.specification
										label="Occupancy"
										value="1-2 guests"
									/>
									<x-product-options-cards.specification
										label="Number of Beds"
										value="1 double or 2 single beds"
									/>
									<x-product-options-cards.specification
										label="Location"
										value="Deck 3"
									/>
									<x-product-options-cards.specification
										label="Cabin Size"
										value="226 sq. ft."
									/>
								</x-product-options-cards.specifications>
								<x-product-options-cards.price
									original_price="$9,395 USD"
									discounted_price="$7,271 USD"
								/>
							</x-product-options-cards.content>
						</x-product-options-cards.card>
					</x-product-options-cards.cards>
					<x-product-options-cards.more-details>
						<x-product-options-cards.card-details id="some-random-id">
							<x-product-options-cards.card-details-title title="Explorer Suite" />
							<x-product-options-cards.description>
								<p>These suites are perfect for people traveling together or solo guests looking to share with like-minded individuals. This suite maximizes interior living space while still offering guests the opportunity to stay connected to the outdoors.</p>
							</x-product-options-cards.description>
							<x-product-options-cards.features title="Features and Standard Amenities: ">
								<p>one double or two single beds, sitting area with sofa bed, picture window, desk, refrigerator, TV, private bathroom with shower and heated floors, hair dryer, bathrobe, slippers, shampoo, conditioner, shower gel, complimentary water bottle.</p>
							</x-product-options-cards.features>
							<x-product-options-cards.gallery :image_ids="[ 32, 34, 36]" :full_size="true" />
							<x-product-options-cards.rooms title="Select Rooms">
								<x-product-options-cards.room>
									<x-product-options-cards.room-title-container>
										<x-product-options-cards.room-title title="Single Room" no_of_guests="1" />
										<x-product-options-cards.room-subtitle subtitle="Price of the cabin for one guest" />
									</x-product-options-cards.room-title-container>
									<x-product-options-cards.room-prices
										original_price="$9,395 USD"
										discounted_price="$7,271 USD"
									/>
								</x-product-options-cards.room>
							</x-product-options-cards.rooms>
							<x-product-options-cards.discounts>
								<x-product-options-cards.discount name="Save 50% - Offer Code 50PROMO" />
							</x-product-options-cards.discounts>
							<x-product-options-cards.cta-buttons>
								<p>Not ready to book? <a>Request a Quote</a></p>
								<x-button size="big" color="black">Request a Callback</x-button>
								<x-button size="big">Book Expedition Now</x-button>
							</x-product-options-cards.cta-buttons>
						</x-product-options-cards.card-details>
						<x-product-options-cards.card-details id="some-random-id-2">
							<x-product-options-cards.card-details-title title="Explorer Suite" />
							<x-product-options-cards.description>
								<p>These suites are perfect for people traveling together or solo guests looking to share with like-minded individuals. This suite maximizes interior living space while still offering guests the opportunity to stay connected to the outdoors.</p>
							</x-product-options-cards.description>
							<x-product-options-cards.features title="Features and Standard Amenities: ">
								<p>one double or two single beds, sitting area with sofa bed, picture window, desk, refrigerator, TV, private bathroom with shower and heated floors, hair dryer, bathrobe, slippers, shampoo, conditioner, shower gel, complimentary water bottle.</p>
							</x-product-options-cards.features>
							<x-product-options-cards.gallery :image_ids="[ 32, 34, 36]" :full_size="true" />
							<x-product-options-cards.rooms title="Select Rooms">
								<x-product-options-cards.room>
									<x-product-options-cards.room-title-container>
										<x-product-options-cards.room-title title="Single Room" no_of_guests="3" />
										<x-product-options-cards.room-subtitle subtitle="Price of the cabin for one guest" />
									</x-product-options-cards.room-title-container>
									<x-product-options-cards.room-prices
										original_price="$9,395 USD"
										discounted_price="$7,271 USD"
									/>
								</x-product-options-cards.room>
							</x-product-options-cards.rooms>
							<x-product-options-cards.discounts>
								<x-product-options-cards.discount name="Save 50% - Offer Code 50PROMO" />
							</x-product-options-cards.discounts>
							<x-product-options-cards.cta-buttons>
								<p>Not ready to book? <a>Request a Quote</a></p>
								<x-button size="big" color="black">Request a Callback</x-button>
								<x-button size="big">Book Expedition Now</x-button>
							</x-product-options-cards.cta-buttons>
						</x-product-options-cards.card-details>
						<x-product-options-cards.card-details id="some-random-id-3">
							<x-product-options-cards.card-details-title title="Explorer Suite" />
							<x-product-options-cards.description>
								<p>These suites are perfect for people traveling together or solo guests looking to share with like-minded individuals. This suite maximizes interior living space while still offering guests the opportunity to stay connected to the outdoors.</p>
							</x-product-options-cards.description>
							<x-product-options-cards.features title="Features and Standard Amenities: ">
								<p>one double or two single beds, sitting area with sofa bed, picture window, desk, refrigerator, TV, private bathroom with shower and heated floors, hair dryer, bathrobe, slippers, shampoo, conditioner, shower gel, complimentary water bottle.</p>
							</x-product-options-cards.features>
							<x-product-options-cards.gallery :image_ids="[ 32, 34, 36]" :full_size="true" />
							<x-product-options-cards.rooms title="Select Rooms">
								<x-product-options-cards.room>
									<x-product-options-cards.room-title-container>
										<x-product-options-cards.room-title title="Single Room" no_of_guests="1" />
										<x-product-options-cards.room-subtitle subtitle="Price of the cabin for one guest" />
									</x-product-options-cards.room-title-container>
									<x-product-options-cards.room-prices
										original_price="$9,395 USD"
										discounted_price="$7,271 USD"
									/>
								</x-product-options-cards.room>
							</x-product-options-cards.rooms>
							<x-product-options-cards.discounts>
								<x-product-options-cards.discount name="Save 50% - Offer Code 50PROMO" />
							</x-product-options-cards.discounts>
							<x-product-options-cards.cta-buttons>
								<p>Not ready to book? <a>Request a Quote</a></p>
								<x-button size="big" color="black">Request a Callback</x-button>
								<x-button size="big">Book Expedition Now</x-button>
							</x-product-options-cards.cta-buttons>
						</x-product-options-cards.card-details>
					</x-product-options-cards.more-details>
				</x-product-options-cards>
			</x-expedition-cards.more-details>
		</x-expedition-cards.card>
	@endforeach
</x-expedition-cards>
