@props( [
	'is_search_page'           => false,
	'destinations_placeholder' => __( 'Anywhere', 'qrk' ),
	'departures_placeholder'   => __( 'I\'m Flexible', 'qrk' ),
	'modal_id'                 => 'search-filters-bar-modal',
	'destinations'             => [],
	'available_months'         => [],
	'filters_api_url'          => '',
	'search_page_url'          => '',
	'image_ids'                => [],
	'cta_urls'                 => [],
	'all_destinations_cta'     => [],
] )

<quark-search-filters-bar
	class="search-filters-bar"
	is-search-page="{{ true === $is_search_page ? 'true' : 'false' }}"
	filters-api-url="{{ $filters_api_url }}"
	search-page-url="{{ $search_page_url }}"
>
	<x-search-filters-bar.search-modal-open-container>
		<x-modal.modal-open :modal_id="$modal_id">
			<x-search-filters-bar.search-modal-open
				label="{!! __( 'Destinations', 'qrk' ) !!}"
				:placeholder="$destinations_placeholder"
				type="destinations"
			/>
		</x-modal.modal-open>
		<x-modal.modal-open :modal_id="$modal_id">
			<x-search-filters-bar.search-modal-open
				label="{{ __( 'Departures', 'qrk' ) }}"
				:placeholder="$departures_placeholder"
				type="departures"
			/>
		</x-modal.modal-open>
	</x-search-filters-bar.search-modal-open-container>
	<x-search-filters-bar.search-button text="{{ __( 'Search Expeditions', 'qrk' ) }}" />
	<x-search-filters-bar.search-modal
		:modal_id="$modal_id"
		:is_search_page="$is_search_page"
		:destinations_placeholder="$destinations_placeholder"
		:departures_placeholder="$departures_placeholder"
		:destinations="$destinations"
		:available_months="$available_months"
		:filters_api_url="$filters_api_url"
		:search_page_url="$search_page_url"
		:image_ids="$image_ids"
		:cta_urls="$cta_urls"
		:all_destinations_cta="$all_destinations_cta"
	/>
	<x-search-filters-bar.sticky-cta />
</quark-search-filters-bar>
