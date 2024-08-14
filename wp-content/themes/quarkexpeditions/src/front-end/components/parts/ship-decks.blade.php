@props( [
	'decks' => [],
] )

@php
	if ( empty( $decks ) ) {
		return;
	}
@endphp

<x-tabs current_tab="{{ $decks[0]['id'] ?? '' }}">
	<x-tabs.header>
		@foreach ( $decks as $deck )
			<x-tabs.nav id="{{ $deck['id'] ?? '' }}" title="{{ $deck['title'] ?? '' }}" />
		@endforeach
	</x-tabs.header>
	<x-tabs.content>
		@foreach ( $decks as $deck )
			<x-tabs.tab id="{{ $deck['id'] ?? '' }}">
				@if ( ! empty( $deck['image_id'] ) )
					<figure>
						<x-image :image_id="$deck['image_id']" :alt="$deck['title'] ?? ''" />
					</figure>
				@endif
				<br />
				<x-content :content="$deck['description'] ?? ''" />
				@if ( ! empty( $deck['cabin_options'] ) )
					<x-media-detail-cards title="{{ __( 'Cabin Details', 'qrk' ) }}">
						@foreach ( $deck['cabin_options'] as $cabin_option )
							<x-media-detail-cards.card>
								<x-two-columns :stack_on_tablet="true">
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
					<x-media-detail-cards title="{{ __( 'Public Spaces', 'qrk' ) }}">
						@foreach ( $deck['public_spaces'] as $public_spaces )
							<x-media-detail-cards.card>
								<x-two-columns :stack_on_tablet="true">
									<x-two-columns.column>
										@if ( ! empty( $public_spaces['image'] ) )
											<x-media-description-cards.image :image_id="$public_spaces['image']" :alt="$public_spaces['title'] ?? ''" />
										@endif
									</x-two-columns.column>
									<x-two-columns.column>
										<x-media-detail-cards.title :title="$public_spaces['title'] ?? ''" heading_level="5" />
										@if ( ! empty( $public_spaces['description'] ) )
											<x-media-detail-cards.content>
												{!! $public_spaces['description'] ?? '' !!}
											</x-media-detail-cards.content>
										@endif
									</x-two-columns.column>
								</x-two-columns>
							</x-media-detail-cards.card>
						@endforeach
					</x-media-detail-cards>
				@endif
			</x-tabs.tab>
		@endforeach
	</x-tabs.content>
</x-tabs>
