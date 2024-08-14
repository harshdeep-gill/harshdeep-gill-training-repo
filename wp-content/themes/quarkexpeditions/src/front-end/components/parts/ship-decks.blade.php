@props( [
	'decks' => [],
] )

@php
	if ( empty( $decks ) ) {
		return;
	}
@endphp

<x-tabs current_tab="">
	<x-tabs.header>
		@foreach ( $decks as $deck )
			<x-tabs.nav id="{{ $deck['id'] ?? '' }}" title="{{ $deck['title'] ?? '' }}" />
		@endforeach
	</x-tabs.header>
	<x-tabs.content>
		@foreach ( $decks as $deck )
			<x-tabs.tab id="{{ $deck['id'] ?? '' }}">
				@if ( ! empty( $deck['image_id'] ) )
					<x-image :image_id="$deck['image_id']" :alt="$deck['title'] ?? ''" />
				@endif
				<x-content :content="$deck['description'] ?? ''" />
				@if ( ! empty( $deck['cabin_options'] ) )
					<x-media-detail-cards title="{{ __( 'Cabin Options', 'qrk' ) }}">
						@foreach ( $deck['cabin_options'] as $cabin_option )
							<x-media-detail-cards.card>
							<x-two-columns>
								<x-two-columns.column>
									@if ( ! empty( $cabin_option['image_id'] ) )
										<x-media-detail-cards.image :image_id="$cabin_option['image_id']" :alt="$cabin_option['title'] ?? ''" />
									@endif
								</x-two-columns.column>
								<x-two-columns.column>
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
								</x-two-columns.column>
							</x-two-columns>
							</x-media-detail-cards.card>
						@endforeach
					</x-media-detail-cards>
				@endif
				@if ( ! empty( $deck['public_spaces'] ) )
					<x-media-description-cards.title title="{{ __( 'Public Spaces & Amenities', 'qrk' ) }}" heading_level="4" />
					<x-media-description-cards :desktop_carousel="true">
						@foreach ( $deck['public_spaces'] as $public_spaces )
							<x-media-description-cards.card>
								<x-two-columns>
									<x-two-columns.column>
										<x-media-description-cards.image :image_id="$public_spaces['image'] ?? 0" :alt="$public_spaces['title'] ?? ''" />
									</x-two-columns.column>
									<x-two-columns.column>
										<x-media-description-cards.content>
											<x-media-description-cards.title :title="$public_spaces['title'] ?? ''" heading_level="5" />
											<x-media-description-cards.description>
												{!! $public_spaces['description'] ?? '' !!}
											</x-media-description-cards.description>
										</x-media-description-cards.content>
									</x-two-columns.column>
								</x-two-columns>
							</x-media-description-cards.card>
						@endforeach
					</x-media-description-cards>
				@endif
			</x-tabs.tab>
		@endforeach
	</x-tabs.content>
</x-tabs>
