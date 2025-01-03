@props( [
	'destinations'         => [],
	'image_ids'            => [],
	'cta_urls'             => [],
	'all_destinations_cta' => [],
] )

@php
	if ( empty( $destinations ) ) {
		return;
	}

	// Display the first two destinations.
	$destinations = array_slice( $destinations, 0, 2 );

	$image_args = [
		'transform' => [
			'height'  => 72,
			'width'   => 72,
			'crop'    => 'fill',
			'gravity' => 'auto',
			'quality' => 100,
		]
	];
@endphp

<quark-search-filters-bar-destinations-filter-options
	class="search-filters-bar__destinations-filter-options"
	active="false"
	destinations="{{ wp_json_encode( $destinations ) }}"
>
	<x-two-columns :border="true">
		@if ( ! empty( $destinations ) )
			@foreach ( $destinations as $destination_item)
				<x-two-columns.column>
					@php
						// Build title.
						$destination_parent_name = $destination_item['label'] ?? '';
						$filters_list_title      = sprintf( __( '%s Regions', 'qrk' ), $destination_parent_name );
					@endphp

					{{-- Filter options. --}}
					<x-menu-list :title="$filters_list_title ?? ''">
						@foreach ( $destination_item['children'] as $child_item )
							@php
								$image_url = '';

								if ( ! empty( $child_item['image_id'] ) ) {
									$image_url = quark_dynamic_image_url( array_merge( [ 'id' => $child_item[ 'image_id' ] ], $image_args ) );
								}
							@endphp
							<quark-search-filters-bar-destinations-option
								class="search-filters-bar__destinations-filter-option"
								label="{{ $child_item['label'] ?? '' }}"
								value="{{ $child_item['value'] ?? '' }}"
								parent="{{ $destination_item['value'] ?? '' }}"
								selected="no"
								disabled="no"
								image-url="{!! esc_url( $image_url ) !!}"
							>
								<figure class="search-filters-bar__destinations-filter-option-image">
									<x-image :image_id="$child_item['image_id'] ?? ''" />
								</figure>
								<x-menu-list.item :title="$child_item['label'] ?? ''" url="#" />
							</quark-search-filters-bar-destinations-option>
						@endforeach
					</x-menu-list>

					{{-- CTA --}}
					@php
						$destination_array_key = strtolower( $destination_parent_name );
					@endphp
					@if (
						array_key_exists(  $destination_array_key, $image_ids ) &&
						array_key_exists(  $destination_array_key, $cta_urls ) &&
						! empty( $image_ids[$destination_array_key] ) &&
						! empty( $cta_urls[$destination_array_key] )
					)
						@php
							$image_id = $image_ids[$destination_array_key];
							$cta_url  = $cta_urls[$destination_array_key]['url'] ?? '#';
							$cta_text = $cta_urls[$destination_array_key]['text'] ?? '';
						@endphp
						<x-thumbnail-cards :is_carousel="false">
							<x-thumbnail-cards.card
								size="small"
								url="{{ $cta_url }}"
								orientation="landscape"
								:image_id="$image_id"
							>
								<x-thumbnail-cards.title :title="$cta_text" align="bottom" />
							</x-thumbnail-cards.card>
						</x-thumbnail-cards>
					@endif
				</x-two-columns.column>
			@endforeach
		@endif

		{{-- Filter options in accordion for mobile --}}
		<x-accordion>
			<x-accordion.item id="search-filters-bar-destinations-accordion" :open="true">
				<x-accordion.item-handle :title="__( 'Destinations', 'qrk' )" />
				<x-accordion.item-content>
					@if ( ! empty( $destinations ) )
						@foreach ( $destinations as $destination_item )
							@php
								// Build title.
								$destination_parent_name = $destination_item['label'] ?? '';
								$filters_list_title      = sprintf( __( '%s Regions', 'qrk' ), $destination_parent_name );
							@endphp

							{{-- Filter options. --}}
							<x-menu-list :title="$filters_list_title ?? ''">
								@foreach ( $destination_item['children'] as $child_item )
									@php
										$image_url = '';

										if ( ! empty( $child_item['image_id'] ) ) {
											$image_url = quark_dynamic_image_url( array_merge( [ 'id' => $child_item[ 'image_id' ] ], $image_args ) );
										}
									@endphp

									<quark-search-filters-bar-destinations-option
										class="search-filters-bar__destinations-filter-option"
										label="{{ $child_item['label'] ?? '' }}"
										value="{{ $child_item['value'] ?? '' }}"
										selected="no"
										disabled="no"
										image-url="{!! esc_url( $image_url ) !!}"
									>
										<figure class="search-filters-bar__destinations-filter-option-image">
											<x-image :image_id="$child_item['image_id'] ?? ''" />
											<x-svg name="check-circle" />
										</figure>
										<x-menu-list.item :title="$child_item['label'] ?? ''" url="#" />
									</quark-search-filters-bar-destinations-option>
								@endforeach
							</x-menu-list>
						@endforeach
					@endif

					{{-- CTA Link --}}
					@if ( ! empty( $all_destinations_cta ) )
						<div class="search-filters-bar__destinations-filter-options-cta">
							<a
								href="{{ $all_destinations_cta['url'] ?? '' }}" class="search-filters-bar__destinations-filter-options-cta-link"
							>
								{{ $all_destinations_cta['text'] ?? __( 'Explore All Destinations', 'qrk' ) }}
							</a>
							<x-svg name="chevron-right" />
						</div>
					@endif
				</x-accordion.item-content>
			</x-accordion.item>
		</x-accordion>
	</x-two-columns>
</quark-search-filters-bar-destinations-filter-options>
