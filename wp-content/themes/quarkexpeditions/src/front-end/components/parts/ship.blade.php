@props( [
	'ship' => [],
] )

@php
	if ( empty( $ship ) || ! is_array( $ship ) ) {
		return;
	}
@endphp

<x-section>
	<x-section.heading>
		<x-section.title :title="$ship['title'] ?? ''" align="left" />
	</x-section.heading>
	<x-section.description>
		{!! $ship['description'] ?? '' !!}
	</x-section.description>
	<x-section.content>
		<x-two-columns>
			<x-two-columns.column>
				@if ( ! empty( $ship['amenities'] ) )
					<h4>{{ __( 'Ship Amenities', 'qrk' ) }}</h4>
					<ul>
						@foreach ( $ship['amenities'] as $amenities )
							<li>{{ $amenities ?? '' }}</li>
						@endforeach
					</ul>
				@endif
			</x-two-columns.column>
			<x-two-columns.column>
				@if ( ! empty( $ship['vessel_features'] ) )
					<h4>{{ __( 'Ship Features', 'qrk' ) }}</h4>
					<ul>
						@foreach ( $ship['vessel_features'] as $vessel_features )
							<li>{{ $vessel_features ?? '' }}</li>
						@endforeach
					</ul>
				@endif
			</x-two-columns.column>
		</x-two-columns>
		{!! $ship['collage'] ?? '' !!}
	</x-section.content>
</x-section>
@if ( ! empty( $ship['decks'] ) )
	<x-drawer.drawer-open :drawer_id="$ship['decks_id'] ?? ''" align="center">
		<x-button type="button" size="big" color="black">
			{{ __( 'View Deck Plans & Cabins', 'qrk' ) }}
		</x-button>
	</x-drawer.drawer-open>
	<x-drawer :id="$ship['decks_id'] ?? ''" compact="true" animation_direction="right">
		<x-drawer.header>
			<h3>{{ __( 'Deck Plans & Cabins', 'qrk' ) }}</h3>
		</x-drawer.header>
		<x-drawer.body>
			<x-tabs current_tab="{{ $ship['decks'][0]['id'] ?? '' }}">
				<x-tabs.header>
					@foreach ( $ship['decks'] as $deck )
						<x-tabs.nav id="{{ $deck['id'] ?? '' }}" title="{{ $deck['title'] ?? '' }}" />
					@endforeach
				</x-tabs.header>
				<x-tabs.content>
					@foreach ( $ship['decks'] as $deck )
						<x-tabs.tab id="{{ $deck['id'] ?? '' }}">
							@if ( ! empty( $deck['image_id'] ) )
								<x-ship-deck-image
									:horizontal_image_id="$deck['image_id']"
									:vertical_image_id="$deck['vertical_image_id'] ?? 0"
									:alt="$deck['title'] ?? ''"
								/>
							@endif
							<x-content :content="$deck['description'] ?? ''" />
							@if ( ! empty( $deck['cabin_options'] ) )
								<x-media-detail-cards :condensed="true" title="{{ __( 'Cabin Options', 'qrk' ) }}">
									@foreach ( $deck['cabin_options'] as $cabin_option )
										<x-media-detail-cards.card>
											@if ( ! empty( $cabin_option['image_id'] ) )
												<x-media-detail-cards.image :image_id="$cabin_option['image_id']" :alt="$cabin_option['title'] ?? ''" />
											@endif
											<x-media-detail-cards.title :title="$cabin_option['title'] ?? ''" heading_level="5" />
											@if ( ! empty( $cabin_option['description'] ) )
												<x-media-detail-cards.content>
													{!! $cabin_option['description'] ?? '' !!}
												</x-media-detail-cards.content>
											@endif
											@if ( ! empty( $cabin_option['details'] ) )
												<x-media-detail-cards.details>
													@foreach ( $cabin_option['details'] as $detail )
														<x-media-detail-cards.detail-item label="{{ $detail['label'] ?? '' }}" value="{!! $detail['value'] ?? '' !!}" />
													@endforeach
												</x-media-detail-cards.details>
											@endif
										</x-media-detail-cards.card>
									@endforeach
								</x-media-detail-cards>
							@endif
							@if ( ! empty( $deck['public_spaces'] ) )
								<x-media-description-cards.title title="{{ __( 'Public Spaces & Amenities', 'qrk' ) }}" heading_level="4" />
								<x-media-description-cards :desktop_carousel="true">
									@foreach ( $deck['public_spaces'] as $public_spaces )
										<x-media-description-cards.card>
											<x-media-description-cards.image :image_id="$public_spaces['image'] ?? 0" :alt="$public_spaces['title'] ?? ''" />
											<x-media-description-cards.content>
												<x-media-description-cards.title :title="$public_spaces['title'] ?? ''" heading_level="5" />
												<x-media-description-cards.description>
													{!! $public_spaces['description'] ?? '' !!}
												</x-media-description-cards.description>
											</x-media-description-cards.content>
										</x-media-description-cards.card>
									@endforeach
								</x-media-description-cards>
							@endif
						</x-tabs.tab>
					@endforeach
				</x-tabs.content>
			</x-tabs>
		</x-drawer.body>
	</x-drawer>
@endif
