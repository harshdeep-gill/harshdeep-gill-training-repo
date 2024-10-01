@php
	if ( empty( $slot ) ) {
		return;
	}

	$dom_id = quark_generate_unique_dom_id();

@endphp

<quark-expedition-search-sticky-filters class="expedition-search__sticky-filters">
	<x-modal.modal-open modal_id="{{ $dom_id }}">
			<x-button type="button" appearance="outline" icon="filters">
				<x-escape :content="__( 'Filter & Sort', 'qrk' )" />
			</x-button>
		</x-modal.modal-open>

	<x-modal id="{{ $dom_id }}" class="expedition-search__filters-modal">
		<x-modal.header>
			<span class="h3"><x-escape :content="__( 'Filter & Sort', 'qrk' )" /></span>
		</x-modal.header>
		<x-modal.body>
			<span class="h4">
				<x-escape :content="__( 'Filters', 'qrk' )" />
			</span>
			{!! $slot !!}
		</x-modal.body>
		<x-modal.footer>
			<x-expedition-search.sidebar-filters.cta-clear />
			<x-expedition-search.sidebar-filters.cta-view-results />
		</x-modal.footer>
	</x-modal>
</quark-expedition-search-sticky-filters>
