@props( [
	'itineraries' => [],
] )

@php
if ( empty( $itineraries ) ) {
	return;
}
@endphp

<x-itinerary-details current_tab="tab-1">
	<x-itinerary-details.tabs-nav>
		@foreach( $itineraries as $itinerary )
			<x-itinerary-details.tabs-nav-item id="{{ $itinerary['tab_id'] }}">
				<x-itinerary-details.tabs-nav-item-title title="{{ $itinerary['tab_title'] }}" />
				<x-itinerary-details.tabs-nav-item-subtitle subtitle="{{ $itinerary['tab_subtitle'] }}" />
			</x-itinerary-details.tabs-nav-item>
		@endforeach
	</x-itinerary-details.tabs-nav>

	<x-itinerary-details.tabs>
		@foreach( $itineraries as $itinerary )
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

							<dt>Ship</dt>
							<dd>
								Ultramarine
								<br>
								<a href="#">Learn more about the ship</a>
							</dd>

							<dt>Starting from</dt>
							<dd>$ X,XXX USD per person</dd>
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
