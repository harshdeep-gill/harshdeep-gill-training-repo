@props( [
	'cards' => [],
] )

<x-departure-cards>
	@foreach( $cards as $card )
		<x-departure-cards.card>
			<x-departure-cards.card-banner text="{{ $card['banner_details']['title'] }}"/>
			<x-departure-cards.header>
				<x-departure-cards.title title="{{ $card['expedition_name'] }}"/>
				<x-departure-cards.promo-tag text="{{ $card['promotion_banner'] }}"/>
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
 										{{ $card['duration_days'] }} <br>
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

					@if ( ! empty( $card['available_offers'] ) )
						<x-departure-cards.offers title="Available Offers">
							@foreach( $card['available_offers'] as $offer )
								<x-departure-cards.offer title="{{ $offer }}"/>
							@endforeach

							<x-departure-cards.offers-modal title="{{ $card['expedition_name'] }}">
								<ul>
									@foreach( $card['available_offers'] as $offer )
										<li>{{ $offer }}</li>
									@endforeach
								</ul>
							</x-departure-cards.offers-modal>
						</x-departure-cards.offers>
					@endif
				</x-departure-cards.body-column>

				<x-departure-cards.body-column>
					<x-departure-cards.price
						original_price="{{ $card['lowest_price']['original_price'] }}"
						discounted_price="{{ $card['lowest_price']['discounted_price'] }}"
					/>

					@if( ! empty( $card['transfer_package_details'] && ! empty( $card['transfer_package_details']['sets'] ) ) )
						<x-departure-cards.transfer_package
							drawer_id="{{ $card['departure_id'] }}"
							drawer_title="Mandatory Transfer Package"
						>
							<p><strong>{{ $card['transfer_package_details']['title'] }}</strong></p>
							<ul>
								@foreach( $card['transfer_package_details']['sets'] as $set )
									<li>{{ $set }}</li>
								@endforeach
							</ul>
							<p><strong>Package Price: {{ $card['transfer_package_details']['price'] }}</strong></p>
						</x-departure-cards.transfer_package>
					@endif
					<x-button size="big"><x-escape :content="__( 'View Cabin Pricing & Options', 'qrk' )"/></x-button>
				</x-departure-cards.body-column>
			</x-departure-cards.body>

			<x-departure-cards.more-details>
				{{-- <x-product-options-cards> // Component Ticket -> https://tuispecialist.atlassian.net/browse/QE-304 --}}
			</x-departure-cards.more-details>
		</x-departure-cards.card>
	@endforeach
</x-departure-cards>
