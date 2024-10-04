@props( [
	'title'        => __( 'Filters', 'qrk' ),
	'filters_data' => [],
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}

	// Expedition keys in order.
	$expedition_keys = [
		'destinations'      => 'Destinations',
		'months'            => 'Departure Months',
		'itinerary_lengths' => 'Itinerary Length',
		'ships'             => 'Ships',
		'adventure_options' => 'Adventure Options',
		'languages'         => 'Languages',
		'expeditions'       => 'Expeditions',
	];

	// Cabin keys in order.
	$cabin_keys = [
		'cabin_classes' => 'Cabin Class',
		'travelers'     => 'Travellers',
	];

@endphp

<quark-expedition-search-sidebar-filters class="expedition-search__sidebar-filters">
	<x-button
		size="big"
		appearance="outline"
		color="white"
		class="expedition-search__sidebar-filters-toggle-button"
		icon="filters"
		icon_position="left"
	>
		{{ __( 'Show Filters', 'qrk' ) }}
	</x-button>
	<div class="expedition-search__sidebar-filters-header">
		<h2 class="h4 expedition-search__sidebar-filters-header-title">
			<x-escape :content="$title" />
			<span class="expedition-search__sidebar-filters-header-selected-count">(X)</span>
		</h2>
		<a href="#">{{ __( 'Hide Filters', 'qrk' ) }}</a>
	</div>
	<div class="expedition-search__sidebar-filters-content">
		<div class="expedition-search__sidebar-filters-content-expedition">
			<h5 class="h5 expedition-search__sidebar-filters-content-title">{{ __( 'Expedition', 'qrk' ) }}</h5>
			<x-accordion>
				@foreach ( $expedition_keys as $key => $title )
					@if ( ! empty( $filters_data[ $key ] ) )
						<x-accordion.item>
							<x-accordion.item-handle title="{{ $title }}" />
							<x-accordion.item-content>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
							</x-accordion.item-content>
						</x-accordion.item>
					@endif
				@endforeach
			</x-accordion>
		</div>
		<div class="expedition-search__sidebar-filters-content-cabin">
			<h5 class="h5 expedition-search__sidebar-filters-content-title">{{ __( 'Cabin', 'qrk' ) }}</h5>
			<x-accordion>
				@foreach ( $cabin_keys as $key => $title )
					@if ( ! empty( $filters_data[ $key ] ) )
						<x-accordion.item>
							<x-accordion.item-handle title="{{ $title }}" />
							<x-accordion.item-content>
								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
							</x-accordion.item-content>
						</x-accordion.item>
					@endif
				@endforeach
			</x-accordion>
		</div>

	</div>
</quark-expedition-search-sidebar-filters>
