@props( [
	'cards' => [],
] )

<x-expedition-cards>
	@if ( ! empty( $cards ) )
		@foreach ( $cards as $card)
			@php
				$departure_id = $card['departure_id'] ?? uniqid();
			@endphp

			<x-expedition-cards.card>
				<x-expedition-cards.card-banner
					text="{{ __( 'Quark Protection Promise', 'qrk' ) }}"
					url="{{ $banner_details['permalink'] ?? '' }}"
				/>

				<x-expedition-cards.grid>
					<x-expedition-cards.grid-column>
						<x-expedition-cards.promo-tag text="{{ $card['promotion_banner'] ?? '' }}" />
						<x-expedition-cards.date>{{ $card['duration_dates'] ?? '' }}</x-expedition-cards.date>
						<x-expedition-cards.title href="{{ $card['expedition_link'] ?? '' }}">{{ $card['expedition_name'] ?? '' }}</x-expedition-cards.title>

						<x-expedition-cards.icons>
							<x-expedition-cards.icon icon="ship">{{ $card['ship_name'] ?? '' }}</x-expedition-cards.icon>

							@if ( ! empty( $card['expedition_categories'] ) )
								@foreach ( $card['expedition_categories'] as $category )
									@if ( 'Fly/Cruise Expeditions' === $category['name'] )
										<x-expedition-cards.icon icon="fly-express">
											{{ __( 'Fly/Cruise Express', 'qrk' ) }}

											@if ( ! empty( $category['description'] ) )
												<x-expedition-cards.tooltip title="{{ $category['name'] ?? '' }}">
													<p><x-escape :content="$category['description']" /></p>
												</x-expedition-cards.tooltip>
											@endif
										</x-expedition-cards.icon>
									@endif
								@endforeach
							@endif
						</x-expedition-cards.icons>

						@if ( ! empty( $card['expedition_slider_images']))
							<x-media-carousel>
								@foreach ( $card['expedition_slider_images'] as $image_id)
									<x-media-carousel.item :image_id="$image_id ?? 0" />
								@endforeach
							</x-media-carousel>
						@endif
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

							@if ( ! empty( $card['starting_from_location'] ) )
								<x-expedition-cards.specification-item>
									<x-expedition-cards.specification-label>
										{{ __( 'Starting from', 'qrk' ) }}
									</x-expedition-cards.specification-label>
									<x-expedition-cards.specification-value>
										{{ $card['starting_from_location'] ?? '' }}
									</x-expedition-cards.specification-value>
								</x-expedition-cards.specification-item>
							@endif

							@if ( ! empty( $card['languages'] ) )
								<x-expedition-cards.specification-item>
									<x-expedition-cards.specification-label>
										{{ __( 'Languages', 'qrk' ) }}
									</x-expedition-cards.specification-label>
									<x-expedition-cards.specification-value>
										{{ $card['languages'] ?? '' }}
									</x-expedition-cards.specification-value>
								</x-expedition-cards.specification-item>
							@endif

							@if ( ! empty( $card['paid_adventure_options'] ) )
								<x-expedition-cards.specification-item>
									<x-expedition-cards.specification-label>
										{{ __( 'Adventure Options', 'qrk' ) }}
									</x-expedition-cards.specification-label>
									<x-expedition-cards.specification-value>
										<x-expedition-cards.adventure-options>
											@if( ! empty( $card['paid_adventure_options'] ) )
												<x-expedition-cards.adventure-option title="{{ array_values( $card['paid_adventure_options'] )[0]['title'] }}"/>
											@endif

											<x-expedition-cards.adventure-options-tooltip :count="count( $card['paid_adventure_options'] ) - 1">
												<ul>
													@foreach( $card['paid_adventure_options'] as $option )
														<li>{{ $option['title'] }}</li>
													@endforeach
												</ul>
											</x-expedition-cards.adventure-options-tooltip>
										</x-expedition-cards.adventure-options>
									</x-expedition-cards.specification-value>
								</x-expedition-cards.specification-item>
							@endif
						</x-expedition-cards.specifications>

						<x-expedition-cards.price
							original_price="{{ $card['lowest_price']['original_price'] ?? '' }}"
							discounted_price="{{ $card['lowest_price']['discounted_price'] ?? '' }}"
						/>

						@if( ! empty( $card['transfer_package_details'] && ! empty( $card['transfer_package_details']['sets'] ) ) )
							<x-expedition-cards.transfer_package
								drawer_id="{{ 'drawer' . $card['departure_id'] }}"
								drawer_title="{!! ! empty( $card['transfer_package_details']['mandatory_transfer_title'] ) ? $card['transfer_package_details']['mandatory_transfer_title'] : __( 'Mandatory Transfer Package', 'qrk') !!}"
								label="{!! $card['transfer_package_details']['offer_inclusion_text'] !!}"
							>
								<p><strong>{{ $card['transfer_package_details']['title'] ?? '' }}</strong></p>
								<ul>
									@foreach( $card['transfer_package_details']['sets'] as $set )
										<li>{!! $set !!}</li>
									@endforeach
								</ul>
								<p>
									<strong>{{ __( 'Package Price: ', 'qrk' ) }} {{ $card['transfer_package_details']['formatted_price'] }}</strong>
								</p>
							</x-expedition-cards.transfer_package>
						@endif

						<x-expedition-cards.buttons>
							<x-expedition-cards.cta :availability_status="$card['departure_status'] ?? ''" />
							<x-button href="{{ $card['expedition_link'] ?? '' }}" color="black" size="big">
								{{ __( 'View Expedition', 'qrk' ) }}
							</x-button>
						</x-expedition-cards.buttons>
					</x-expedition-cards.grid-column>
				</x-expedition-cards.grid>

				@if ( ! empty( $card['cabins'] ) && is_array( $card['cabins'] ) )
					<x-expedition-cards.more-details>
						<h4>
							<x-escape :content="__( 'Cabins Options', 'qrk' )"/>
						</h4>
						<x-product-options-cards>
							<x-product-options-cards.cards :request_a_quote_url="$card['request_a_quote_url'] ?? ''">
								@foreach( $card['cabins'] as $cabin_code => $cabin )
									@php
										$cabin_availability_status = $cabin['specifications']['availability_status'] ?? 'U';

										if ( empty( $cabin_availability_status ) || 'U' === $cabin_availability_status ) {
											continue;
										}
									@endphp
									<x-product-options-cards.card details_id="{{  $cabin_code . '_' . $card['departure_id'] }}" :status="$cabin_availability_status" type="{{ $cabin['type'] ?? '' }}" >

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
															:label="__( 'Occupancy', 'qrk' )"
															value="{{ $cabin['specifications']['occupancy'] }}"
														/>
													@endif

													@if( ! empty( $cabin['specifications']['bed_configuration'] ) )
														<x-product-options-cards.specification
															:label="__( 'Number of Beds', 'qrk' )"
															value="{{ $cabin['specifications']['bed_configuration'] }}"
														/>
													@endif

													@if( ! empty( $cabin['specifications']['location'] ) )
														<x-product-options-cards.specification
															:label="__( 'Location', 'qrk' )"
															value="{{ $cabin['specifications']['location'] }}"
														/>
													@endif

													@if( ! empty( $cabin['specifications']['size'] ) )
														<x-product-options-cards.specification
															:label="__( 'Cabin Size', 'qrk' )"
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

											@if( ! empty( $card['transfer_package_details'] && ! empty( $card['transfer_package_details']['sets'] ) ) )
												<x-product-options-cards.transfer-package>
													<x-departure-cards.transfer_package
														drawer_id="transfer-price-{{ $cabin_code . '_' . $departure_id }}"
														:drawer_title="__( 'Mandatory Transfer Package', 'qrk' )"
														label="{!! $card['transfer_package_details']['offer_inclusion_text'] !!}"
													>
														<p><strong>{{ $card['transfer_package_details']['title'] ?? '' }}</strong></p>
														<ul>
															@foreach( $card['transfer_package_details']['sets'] as $set )
																<li>{!! $set !!}</li>
															@endforeach
														</ul>
														<p>
															<strong>{{ __( 'Package Price:', 'qrk' ) }} {{ $card['transfer_package_details']['formatted_price'] ?? '' }}</strong>
														</p>
													</x-departure-cards.transfer_package>

													<div class="product-options-cards__tooltip">
														<span>
															{!! $card['transfer_package_details']['offer_inclusion_text'] ? $card['transfer_package_details']['offer_inclusion_text'] : __( 'Incl. Transfer Package', 'qrk' ) !!}
														</span>
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
													</div>
												</x-product-options-cards.transfer-package>
											@endif

										</x-product-options-cards.content>

										{{-- Mobile Dialog CTA --}}
										<x-product-options-cards.cta-dialog dialog_id="dialog-{{  $cabin_code . '_' . $departure_id }}">
											{{ __( 'View', 'qrk' ) }}
										</x-product-options-cards.cta-dialog>

										{{-- Modal dialog --}}
										<x-product-options-cards.dialog id="dialog-{{  $cabin_code . '_' . $departure_id }}" class="product-options-cards__card-details">
											<x-dialog.header>
												<h3>{{ $cabin['name'] ?? '' }}</h3>
											</x-dialog.header>

											<x-dialog.body>
												@if( ! empty( $cabin['description'] ) )
													<x-product-options-cards.description>
														{!! $cabin['description'] !!}
													</x-product-options-cards.description>
												@endif

												@if( ! empty( $cabin['gallery'] ) )
													<x-product-options-cards.gallery :image_ids="$cabin['gallery']" :full_size="true"/>
												@endif

												@if( ! empty( $cabin['occupancies'] ) && is_array( $cabin['occupancies'] ) )
													<x-product-options-cards.rooms :title="__( 'Select Rooms', 'qrk' )">
														@foreach( $cabin['occupancies'] as $index => $occupancy )
															<x-product-options-cards.room :checkout_url="$occupancy['checkout_url'] ?? ''" id="modal-room-type-id-{{ $departure_id }}-{{ $cabin_code }}-{{ $index }}" name="modal-room-type-{{ $departure_id }}-{{ $cabin_code }}" checked="{{ $index == 0 ? 'checked' : '' }}" mask="{{ $occupancy['name'] ?? '' }}">
																<x-product-options-cards.room-title-container>
																	@if( ! empty( $occupancy['description'] ) )
																		<x-product-options-cards.room-title
																			title="{{ $occupancy['description'] }}"
																			no_of_guests="{{ $occupancy['no_of_guests'] ?? '' }}"
																		/>
																	@endif
																	<x-product-options-cards.room-subtitle :subtitle="__( 'Price of the cabin for one guest', 'qrk' )"/>
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

												{{-- Promo Description --}}
												@if( ! empty( $card['promotions'] ) && is_array( $card['promotions'] ) && ! empty( $cabin['promo_codes'] ) && is_array( $cabin['promo_codes'] ) )
													<x-product-options-cards.discounts>
														@foreach( $cabin['promo_codes'] as $promo_code )
															@if( ! empty( $promo_code ) && ! empty( $card['promotions'][$promo_code] ) )
																<x-product-options-cards.discount name="{{ $card['promotions'][$promo_code] }}"/>
															@endif
														@endforeach
													</x-product-options-cards.discounts>
												@endif

												<p class="product-options-cards__help-text">{{ __( 'Not ready to book?', 'qrk' ) }} <a href="{!! esc_url( $card['request_a_quote_url'] ) !!}">{{ __( 'Request a quote', 'qrk' ) }}</a></p>
											</x-dialog.body>

											@if ( ! empty( $cabin['occupancies'] ) && is_array( $cabin['occupancies'] ) && is_array( $cabin['occupancies'][0] ) && ! empty( $cabin['occupancies'][0]['checkout_url'] ) )
												<x-dialog.footer>
													<x-product-options-cards.cta-buttons>
														<x-product-options-cards.phone-number />
															<x-product-options-cards.cta-book-now :url="$cabin['occupancies'][0]['checkout_url']" />
													</x-product-options-cards.cta-buttons>
												</x-dialog.footer>
											@endif
										</x-product-options-cards.dialog>

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
										id="{{ $cabin_code . '_' . $card['departure_id'] }}">

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
												@foreach( $cabin['occupancies'] as $index => $occupancy )
													<x-product-options-cards.room checkout_url="{!! $occupancy['checkout_url'] !!}" name="room-type-{{ $card['departure_id'] }}-{{ $cabin_code }}" checked="{{ $index == 0 ? 'checked' : '' }}" mask="{{ $occupancy['name'] ?? '' }}">
														<x-product-options-cards.room-title-container>
															@if( ! empty( $occupancy['description'] ) )
																<x-product-options-cards.room-title
																	title="{{ $occupancy['description'] }}"
																	no_of_guests="{{ $occupancy['no_of_guests'] ?? '' }}"
																/>
															@endif
															<x-product-options-cards.room-subtitle :subtitle="__( 'Price of the cabin for one guest', 'qrk' )"/>
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

										{{-- Promo Description --}}
										@if( ! empty( $card['promotions'] ) && is_array( $card['promotions'] ) && ! empty( $cabin['promo_codes'] ) && is_array( $cabin['promo_codes'] ) )
											<x-product-options-cards.discounts>
												@foreach( $cabin['promo_codes'] as $promo_code )
													@if( ! empty( $promo_code ) && ! empty( $card['promotions'][$promo_code] ) )
														<x-product-options-cards.discount name="{{ $card['promotions'][$promo_code] }}"/>
													@endif
												@endforeach
											</x-product-options-cards.discounts>
										@endif

										<x-product-options-cards.cta-buttons>
											@if ( ! empty( $card['request_a_quote_url'] ) )
												<p class="product-options-cards__help-text">{{ __( 'Not ready to book?', 'qrk' ) }} <a href="{!! esc_url( $card['request_a_quote_url'] ) !!}">{{ __( 'Request a quote', 'qrk' ) }}</a></p>
											@endif
											<x-product-options-cards.phone-number />
											@if ( ! empty( $cabin['occupancies'] ) && is_array( $cabin['occupancies'] ) && is_array( $cabin['occupancies'][0] ) && ! empty( $cabin['occupancies'][0]['checkout_url'] ) )
												<x-product-options-cards.cta-book-now :url="$cabin['occupancies'][0]['checkout_url'] ?? '#'" />
											@endif
										</x-product-options-cards.cta-buttons>
									</x-product-options-cards.card-details>
								@endforeach
							</x-product-options-cards.more-details>
						</x-product-options-cards>
					</x-expedition-cards.more-details>
				@endif
			</x-expedition-cards.card>
		@endforeach
	@else
		<x-expedition-search.results.no-results />
	@endif
</x-expedition-cards>
