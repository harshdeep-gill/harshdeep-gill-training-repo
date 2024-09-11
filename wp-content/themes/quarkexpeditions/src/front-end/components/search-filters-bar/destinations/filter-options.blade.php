@props( [
	// TODO: Fetch from backend.
	'destinations' => [
		[
			'id' => 1,
			'name' => 'Antartic',
			'slug' => 'antartic',
			'children' => [
				[
					'id' => 2,
					'name' => 'South Pole',
					'slug' => 'south-pole',
					'parent_id' => 1,
					'image_id'  => 31,
				],
				[
					'id' => 3,
					'name' => 'Spitsbergen',
					'slug' => 'spitsbergen',
					'parent_id' => 1,
					'image_id' => 32
				],
				[
					'id' => 2,
					'name' => 'South Pole',
					'slug' => 'south-pole',
					'parent_id' => 1,
					'image_id'  => 31,
				],
				[
					'id' => 3,
					'name' => 'Spitsbergen',
					'slug' => 'spitsbergen',
					'parent_id' => 1,
					'image_id' => 32
				],
				[
					'id' => 2,
					'name' => 'South Pole',
					'slug' => 'south-pole',
					'parent_id' => 1,
					'image_id'  => 31,
				],
				[
					'id' => 3,
					'name' => 'Spitsbergen',
					'slug' => 'spitsbergen',
					'parent_id' => 1,
					'image_id' => 32
				],
				[
					'id' => 2,
					'name' => 'South Pole',
					'slug' => 'south-pole',
					'parent_id' => 1,
					'image_id'  => 31,
				],
				[
					'id' => 3,
					'name' => 'Spitsbergen',
					'slug' => 'spitsbergen',
					'parent_id' => 1,
					'image_id' => 32
				],
				[
					'id' => 2,
					'name' => 'South Pole',
					'slug' => 'south-pole',
					'parent_id' => 1,
					'image_id'  => 31,
				],
				[
					'id' => 3,
					'name' => 'Spitsbergen',
					'slug' => 'spitsbergen',
					'parent_id' => 1,
					'image_id' => 32
				],
			],
		],
		[
			'id' => 4,
			'name' => 'Arctic',
			'slug' => 'arctic',
			'children' => [
				[
					'id' => 5,
					'name' => 'Greenland',
					'slug' => 'greenland',
					'parent_id' => 4,
					'image_id'  => 31,
				],
				[
					'id' => 6,
					'name' => 'Iceland',
					'slug' => 'iceland',
					'parent_id' => 4,
					'image_id'  => 32,
				],
			],
		]
	],
	'cta_image_id' => 0,
	'cta_url'      => '',
] )

@php
	if ( empty( $destinations ) ) {
		return;
	}
@endphp

<quark-search-filters-bar-destinations-options
	class="search-filters-bar__destinations-filter-options"
	selected="false"
>
	<x-two-columns :border="true">
		@if ( ! empty( $destinations[0] ) )
			<x-two-columns.column>
				@php
					// Build title.
					$destination_parent_name = $destinations[0]['name'] ?? '';
					$filters_list_title      = sprintf( __( '%s Regions', 'qrk' ), $destination_parent_name );
				@endphp

				{{-- Filter options. --}}
				<x-menu-list :title="$filters_list_title ?? ''">
					@foreach ( $destinations[0]['children'] as $child_item )
						<quark-search-filters-bar-destinations-option
							class="search-filters-bar__destinations-filter-option"
							value="{{ $child_item['slug'] ?? '' }}"
						>
							<figure class="search-filters-bar__destinations-filter-option-image">
								<x-image :image_id="$child_item['image_id'] ?? ''" />
							</figure>
							<x-menu-list.item :title="$child_item['name'] ?? ''" url="#" />
						</quark-search-filters-bar-destinations-option>
					@endforeach
				</x-menu-list>

				{{-- CTA --}}
				<x-thumbnail-cards :is_carousel="false">
					<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="29">
						<x-thumbnail-cards.title title="View All Antarctic Expeditions" align="bottom" />
					</x-thumbnail-cards.card>
				</x-thumbnail-cards>
			</x-two-columns.column>
		@endif
		@if ( ! empty( $destinations[1] ) )
			<x-two-columns.column>
				@php
					// Build title.
					$destination_parent_name = $destinations[1]['name'] ?? '';
					$filters_list_title      = sprintf( __( '%s Regions', 'qrk' ), $destination_parent_name );
				@endphp

				{{-- Filter options. --}}
				<x-menu-list :title="$filters_list_title ?? ''">
					@foreach ( $destinations[1]['children'] as $child_item )
						<quark-search-filters-bar-destinations-option
							class="search-filters-bar__destinations-filter-option"
							value="{{ $child_item['slug'] ?? '' }}"
						>
							<figure class="search-filters-bar__destinations-filter-option-image">
								<x-image :image_id="$child_item['image_id'] ?? ''" />
							</figure>
							<x-menu-list.item :title="$child_item['name'] ?? ''" url="#" />
						</quark-search-filters-bar-destinations-option>
					@endforeach
				</x-menu-list>

				{{-- CTA --}}
				<x-thumbnail-cards :is_carousel="false">
					<x-thumbnail-cards.card size="small" url="#" orientation="landscape" image_id="30">
						<x-thumbnail-cards.title title="View All Arctic Expeditions" align="bottom" />
					</x-thumbnail-cards.card>
				</x-thumbnail-cards>
			</x-two-columns.column>
		@endif

		{{-- Filter options in accordion for mobile --}}
		<x-accordion>
			<x-accordion.item id="accordion-region-season">
				<x-accordion.item-handle title="Destinations" />
				<x-accordion.item-content>
					<x-menu-list :title="$filters_list_title ?? ''">
						@foreach ( $destinations[0]['children'] as $child_item )
							<quark-search-filters-bar-destinations-option
								class="search-filters-bar__destinations-filter-option"
								value="{{ $child_item['slug'] ?? '' }}"
							>
								<figure class="search-filters-bar__destinations-filter-option-image">
									<x-image :image_id="$child_item['image_id'] ?? ''" />
								</figure>
								<x-menu-list.item :title="$child_item['name'] ?? ''" url="#" />
							</quark-search-filters-bar-destinations-option>
						@endforeach
					</x-menu-list>

					<x-menu-list :title="$filters_list_title ?? ''">
						@foreach ( $destinations[1]['children'] as $child_item )
							<quark-search-filters-bar-destinations-option
								class="search-filters-bar__destinations-filter-option"
								value="{{ $child_item['slug'] ?? '' }}"
							>
								<figure class="search-filters-bar__destinations-filter-option-image">
									<x-image :image_id="$child_item['image_id'] ?? ''" />
								</figure>
								<x-menu-list.item :title="$child_item['name'] ?? ''" url="#" />
							</quark-search-filters-bar-destinations-option>
						@endforeach
					</x-menu-list>
				</x-accordion.item-content>
			</x-accordion.item>
		</x-accordion>
	</x-two-columns>
</quark-search-filters-bar-destinations-options>