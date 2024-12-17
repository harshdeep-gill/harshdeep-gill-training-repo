@props( [
	'modal_id'                 => 'search-filters-bar-modal',
	'is_search_page'           => false,
	'destinations_placeholder' => __( 'Anywhere', 'qrk' ),
	'departures_placeholder'   => __( 'I\'m Flexible', 'qrk' ),
	'destinations'             => [],
	'available_months'         => [],
	'filters_api_url'          => '',
	'search_page_url'          => '',
	'image_ids'                => [],
	'cta_urls'                 => [],
	'all_destinations_cta'     => [],
] )

{{-- Render the modal only once even if there are multiple search bars present on a page --}}
<x-once id="{{ $modal_id }}">
	<x-modal :id="$modal_id" class="search-filters-bar__modal" >
		<x-modal.header>
			<h3>{{ __( 'Select your Preferences', 'qrk' ) }}</h3>
		</x-modal.header>
		<x-modal.body>
			<quark-search-filters-bar
				class="search-filters-bar"
				is-search-page="{{ true === $is_search_page ? 'true' : 'false' }}"
				filters-api-url="{{ $filters_api_url }}"
				search-page-url="{{ $search_page_url }}"
			>
				<div class="search-filters-bar__modal-filters-container">
					<x-search-filters-bar.destinations />
					<x-search-filters-bar.departure-months />
				</div>
				<x-search-filters-bar.search-button text="{{ __( 'Search Expeditions', 'qrk' ) }}" />
			</quark-search-filters-bar>

			{{-- Filter options --}}
			<x-search-filters-bar.destinations.filter-options
				:destinations="$destinations"
				:image_ids="$image_ids"
				:cta_urls="$cta_urls"
				:all_destinations_cta="$all_destinations_cta"
			/>
			<x-search-filters-bar.departure-months.filter-options :available_months="$available_months" />
		</x-modal.body>
		<x-modal.footer>
			<x-buttons class="search-filters-bar__modal-buttons">
				<x-button
					size="big"
					appearance="outline"
					class="search-filters-bar__modal-button-clear-all"
				>
					{{ __( 'Clear All', 'qrk' ) }}
				</x-button>
				<x-button size="big" class="search-filters-bar__modal-button-search">
					{{ __( 'View Expeditions', 'qrk' ) }}
				</x-button>
			</x-buttons>
		</x-modal.footer>
	</x-modal>
</x-once>
