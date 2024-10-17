@props( [
	'modal_id' => 'search-filters-bar-modal',
	'cta_text' => __( 'Search Expeditions', 'qrk' ),
] )

<quark-search-filters-bar-sticky-cta class="search-filters-bar__sticky-cta">
	<x-modal.modal-open :modal_id="$modal_id">
		<x-button size="big">
			{{ $cta_text }}
		</x-button>
	</x-modal.modal-open>
</quark-search-filters-bar-sticky-cta>