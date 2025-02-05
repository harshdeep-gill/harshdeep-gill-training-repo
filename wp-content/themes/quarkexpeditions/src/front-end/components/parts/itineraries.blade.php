@props( [
	'itinerary_groups' => [],
	'active_tab'       => 0,
] )

@php
if ( empty( $itinerary_groups ) ) {
	return;
}
@endphp

<x-tabs current_tab="{{ $active_tab }}" update_url="no">
	<x-tabs.header>
		@foreach( $itinerary_groups as $itinerary_group )
			@if( empty( $itinerary_group['tab_id'] ) )
				@continue;
			@endif

			<x-tabs.nav
				active="{{ $active_tab === $itinerary_group['tab_id'] }}"
				id="{{ $itinerary_group['tab_id'] }}"
				title="{{ $itinerary_group['tab_title'] ?? '' }}"
			/>
		@endforeach
	</x-tabs.header>

	<x-tabs.content>
		@foreach( $itinerary_groups as $itinerary_group )
			@if( empty( $itinerary_group['tab_id'] ) )
				@continue;
			@endif

			<x-tabs.tab id="{{ $itinerary_group['tab_id'] }}" open="{{ $active_tab === $itinerary_group['tab_id'] }}">

				<x-itinerary-details.modal-nav>
					@foreach( $itinerary_group['itineraries'] as $itinerary )
						@if( empty( $itinerary['tab_id'] ) )
							@continue;
						@endif

						<x-itinerary-details.modal-nav-item modal_id="modal-{{ $itinerary['tab_id'] }}">
							<x-itinerary-details.tabs-nav-item-title title="{{ $itinerary['tab_title'] ?? 'h' }}" />
							<x-itinerary-details.tabs-nav-item-subtitle subtitle="{{ $itinerary['tab_subtitle'] ?? '' }}" />
						</x-itinerary-details.modal-nav-item>

						<x-modal id="modal-{{ $itinerary['tab_id'] }}" class="itinerary-details__modal">
							@if ( ! empty($itinerary['tab_content_header'] ) )
								<x-modal.header>
									<h3 class="departure-cards__modal-title"><x-escape :content="$itinerary['tab_content_header']" /></h3>
								</x-modal.header>
							@endif

							<x-modal.body>
								<x-itinerary-details.summary>
									<x-itinerary-details.summary-content>
										<dl>
											<dt>{{ __( 'Duration', 'qrk' ) }}</dt>
											<dd>{{ $itinerary['duration'] ?? '' }}</dd>

											<dt>{{ __( 'Departing from', 'qrk' ) }}</dt>
											<dd>{{ $itinerary['departing_from'] ?? '' }}</dd>

											@if( ! empty( $itinerary['ships'] ) )
												<dt>{{ __( 'Ship', 'qrk' ) }}</dt>
												@foreach( $itinerary['ships'] as $ship )
													<dd>
														{{ $ship['name'] }}
														<br>
														<a href="{{ $ship['link'] }}">{{ __( 'Learn more about the ship', 'qrk' ) }}</a>
													</dd>
												@endforeach
											@endif

											@if( ! empty( $itinerary['price'] ) )
												<dt>{{ __( 'Starting from', 'qrk' ) }}</dt>
												<dd>{{ $itinerary['price'] }} </dd>
											@endif
										</dl>
										@if( ! empty( $itinerary['brochure'] ) )
											<x-itinerary-details.download-button url="{{ $itinerary['brochure'] }}" />
										@endif
									</x-itinerary-details.summary-content>
									@if( ! empty( $itinerary['map'] ) )
										<x-itinerary-details.map-lightbox name="map-lightbox" image_id="{{ $itinerary['map'] }}" />
									@endif
								</x-itinerary-details.summary>
								<x-itinerary-details.details>
									<x-accordion :full_border="false">
										@foreach ( $itinerary['itinerary_days'] as $itinerary_day )
											<x-accordion.item :open="false">
												<x-accordion.item-handle :title="$itinerary_day['title'] ?? ''" />
												<x-accordion.item-content>
													{!! $itinerary_day['content'] ?? '' !!}
												</x-accordion.item-content>
											</x-accordion.item>
										@endforeach
									</x-accordion>
								</x-itinerary-details.details>
							</x-modal.body>

							<x-modal.footer>
								<x-itinerary-details.cta>
										@if ( ! empty( $itinerary['request_a_quote_url'] ) )
											<x-button size="big" :href="$itinerary['request_a_quote_url']">{{ __( 'Request a Quote', 'qrk' ) }}</x-button>
										@endif
										@if( ! empty( $itinerary['brochure'] ) )
											<x-itinerary-details.download-button url="{{ $itinerary['brochure'] }}" />
										@endif
									</x-itinerary-details.cta>
							</x-modal.footer>
						</x-modal>
					@endforeach
				</x-itinerary-details.modal-nav>

				<x-itinerary-details current_tab="{{ $itinerary_group['active_tab'] ?? '' }}">
					<x-itinerary-details.tabs-nav>
						@foreach( $itinerary_group['itineraries'] as $itinerary )
							@if( empty( $itinerary['tab_id'] ) )
								@continue;
							@endif

							<x-itinerary-details.tabs-nav-item id="{{ $itinerary['tab_id'] }}" active="{{ $itinerary_group['active_tab'] === $itinerary['tab_id'] }}">
								<x-itinerary-details.tabs-nav-item-title title="{{ $itinerary['tab_title'] ?? 'h' }}" />
								<x-itinerary-details.tabs-nav-item-subtitle subtitle="{{ $itinerary['tab_subtitle'] ?? '' }}" />
							</x-itinerary-details.tabs-nav-item>
						@endforeach
					</x-itinerary-details.tabs-nav>

					<x-itinerary-details.tabs>
						@foreach( $itinerary_group['itineraries'] as $itinerary )
							@if( empty( $itinerary['tab_id'] ) )
								@continue;
							@endif

							<x-itinerary-details.tab id="{{ $itinerary['tab_id'] }}" open="{{ $itinerary_group['active_tab'] === $itinerary['tab_id'] }}">
								<x-itinerary-details.header title="{{ $itinerary['tab_content_header'] ?? '' }}" />

								<x-itinerary-details.body>
									<x-itinerary-details.summary>
										<x-itinerary-details.summary-content>
											<dl>
												<dt>{{ __( 'Duration', 'qrk' ) }}</dt>
												<dd>{{ $itinerary['duration'] ?? '' }}</dd>

												<dt>{{ __( 'Departing from', 'qrk' ) }}</dt>
												<dd>{{ $itinerary['departing_from'] ?? '' }}</dd>

												@if( ! empty( $itinerary['ships'] ) )
													<dt>{{ __( 'Ship', 'qrk' ) }}</dt>
													@foreach( $itinerary['ships'] as $ship )
														<dd>
															{{ $ship['name'] }}
															<br>
															<a href="{{ $ship['link'] }}">{{ __( 'Learn more about the ship', 'qrk' ) }}</a>
														</dd>
													@endforeach
												@endif

												@if( ! empty( $itinerary['price'] ) )
													<dt>{{ __( 'Starting from', 'qrk' ) }}</dt>
													<dd>{{ $itinerary['price'] }} </dd>
												@endif
											</dl>
											@if( ! empty( $itinerary['brochure'] ) )
												<x-itinerary-details.download-button url="{{ $itinerary['brochure'] }}" />
											@endif
										</x-itinerary-details.summary-content>
										@if( ! empty( $itinerary['map'] ) )
											<x-itinerary-details.map-lightbox name="map-lightbox" image_id="{{ $itinerary['map'] }}" />
										@endif
									</x-itinerary-details.summary>
									<x-itinerary-details.details>
										<x-accordion :full_border="false">
											@foreach ( $itinerary['itinerary_days'] as $itinerary_day )
												<x-accordion.item :open="false">
													<x-accordion.item-handle :title="$itinerary_day['title'] ?? ''" />
													<x-accordion.item-content>
														{!! $itinerary_day['content'] ?? '' !!}
													</x-accordion.item-content>
												</x-accordion.item>
											@endforeach
										</x-accordion>
									</x-itinerary-details.details>
								</x-itinerary-details.body>

								<x-itinerary-details.footer>
									<x-itinerary-details.cta>
										@if ( ! empty( $itinerary['request_a_quote_url'] ) )
											<x-button size="big" :href="$itinerary['request_a_quote_url']">{{ __( 'Request a Quote', 'qrk' ) }}</x-button>
										@endif
										@if( ! empty( $itinerary['brochure'] ) )
											<x-itinerary-details.download-button url="{{ $itinerary['brochure'] }}" />
										@endif
									</x-itinerary-details.cta>
								</x-itinerary-details.footer>
							</x-itinerary-details.tab>
						@endforeach
					</x-itinerary-details.tabs>
				</x-itinerary-details>
			</x-tabs.tab>
		@endforeach
	</x-tabs.content>
</x-tabs>
