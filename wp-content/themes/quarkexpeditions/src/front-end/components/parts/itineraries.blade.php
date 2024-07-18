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
			<x-tabs.nav
				id="{{ $itinerary_group['tab_id'] }}"
				title="{{ $itinerary_group['tab_title'] }}"
			/>
		@endforeach
	</x-tabs.header>

	<x-tabs.content>
		@foreach( $itinerary_groups as $itinerary_group )
			<x-tabs.tab id="{{ $itinerary_group['tab_id'] }}">
				<x-itinerary-details current_tab="{{ $itinerary_group['active_tab'] }}">
					<x-itinerary-details.tabs-nav>
						@foreach( $itinerary_group['itineraries'] as $itinerary )
							<x-itinerary-details.tabs-nav-item id="{{ $itinerary['tab_id'] }}">
								<x-itinerary-details.tabs-nav-item-title title="{{ $itinerary['tab_title'] }}" />
								<x-itinerary-details.tabs-nav-item-subtitle subtitle="{{ $itinerary['tab_subtitle'] }}" />
							</x-itinerary-details.tabs-nav-item>
						@endforeach
					</x-itinerary-details.tabs-nav>

					<x-itinerary-details.tabs>
						@foreach( $itinerary_group['itineraries'] as $itinerary )
							<x-itinerary-details.tab id="{{ $itinerary['tab_id'] }}">
								<x-itinerary-details.header title="{{ $itinerary['tab_content_header'] ?? '' }}" />

								<x-itinerary-details.body>
									<x-itinerary-details.summary>
										<x-itinerary-details.summary-content>
											<dl>
												<dt>Duration</dt>
												<dd>{{ $itinerary['duration'] ?? '' }}</dd>

												<dt>Departing from</dt>
												<dd>{{ $itinerary['departing_from'] ?? '' }}</dd>

												@if( ! empty( $itinerary['ships'] ) )
													<dt>Ship</dt>
													@foreach( $itinerary['ships'] as $ship )
														<dd>
															{{ $ship['name'] }}
															<br>
															<a href="{{ $ship['link'] }}">Learn more about the ship</a>
														</dd>
													@endforeach
												@endif

												<dt>Starting from</dt>
												<dd>${{ $itinerary['price'] }} USD per person</dd>
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
										<x-button size="big" href="#">Request a Quote</x-button>
										<x-itinerary-details.download-button url="#" />
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
