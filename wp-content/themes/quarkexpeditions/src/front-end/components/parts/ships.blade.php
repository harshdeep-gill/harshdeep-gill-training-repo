@props( [
	'ships' => [],
] )

@php
	if ( empty( $ships ) ) {
		return;
	}
@endphp

<x-tabs update_url='yes'>
	<x-tabs.header>
		@foreach ( $ships as $ship )
			@if ( ! empty( $ship['id'] ) )
				<x-tabs.nav id="{{ $ship['id'] }}" title="{{ $ship['title'] ?? '' }}" />
			@endif
		@endforeach
	</x-tabs.header>

	<x-tabs.content>
		@foreach ( $ships as $ship )
			@if ( ! empty( $ship['id'] ) )
				<x-tabs.tab id="{{ $ship['id'] }}">
					<x-section class="section--ship">
						<x-section.heading>
							<x-section.title :title="$ship['title'] ?? ''" align="left" />
						</x-section.heading>
						<x-section.description>
							{!! $ship['description'] !!}
						</x-section.description>
						<x-section.content>
							{{ $ship['content'] }}
						</x-section.content>
					</x-section>
					<x-drawer.drawer-open :drawer_id="$ship['decks_id']">
						<x-button type="button" size="big" color="black">
							{{ __( 'View Deck Plans & Cabins', 'qrk' ) }}
						</x-button>
					</x-drawer.drawer-open>
					<x-drawer :id="$ship['decks_id']" content_class="drawer__content--half-size" animation_direction="right">
						<x-drawer.header>
							<h3>{{ __( 'Deck Plans & Cabins', 'qrk' ) }}</h3>
						</x-drawer.header>
						<x-drawer.body>
							<x-tabs>
								<x-tabs.header>
									@foreach ( $ship['decks'] as $deck )
										<x-tabs.nav id="{{ $deck['id'] }}" title="{{ $deck['title'] ?? '' }}" />
									@endforeach
								</x-tabs.header>
								<x-tabs.content>
									@foreach ( $ship['decks'] as $deck )
										<x-tabs.tab id="{{ $deck['id'] }}">
											@if ( ! empty( $deck['image_id'] ) )
												<x-image :image_id="$deck['image_id']" :alt="$deck['title'] ?? ''" />
											@endif
											<x-content :content="$deck['description'] ?? ''" />
											@if ( ! empty( $deck['cabin_options'] ) )
												<h3>{{ __( 'Cabin Options', 'qrk' ) }}</h3>
												<x-media-detail-cards>
													@foreach ( $deck['cabin_options'] as $cabin_option )
														<x-media-detail-cards.card>
															@if ( ! empty( $cabin_option['image_id'] ) )
																<x-media-detail-cards.image :image_id="$cabin_option['image_id']" :alt="$cabin_option['title']" />
															@endif
															<h5>{{ $cabin_option['title'] ?? '' }}</h5>
															@if ( ! empty( $cabin_option['description'] ) )
																<x-media-detail-cards.content>
																	{!! $cabin_option['description'] !!}
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
												<h3>{{ __( 'Public Spaces & Amenities', 'qrk' ) }}</h3>
												<x-media-description-cards>
													@foreach ( $deck['public_spaces'] as $public_spaces )
														<x-media-description-cards.card>
															<x-media-description-cards.image :image_id="$public_spaces['image']" :alt="$public_spaces['title'] ?? ''" />
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
				</x-tabs.tab>
			@endif
		@endforeach
	</x-tabs.content>
</x-tabs>
