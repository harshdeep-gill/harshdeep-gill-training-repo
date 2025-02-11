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
			<quark-expedition-search-filters>
				<x-form.field class="expedition-search__filters-sort">
					<x-form.select label="{{ __( 'Sort by:', 'qrk' ) }}">
						<x-form.option value="date-now" label="{{ __( 'Date (upcoming to later)', 'qrk' ) }}" selected="yes">
							{{ __( 'Date (upcoming to later)', 'qrk' ) }}
						</x-form.option>
						<x-form.option value="date-later" label="{{ __( 'Date (later to upcoming)', 'qrk' ) }}">
							{{ __( 'Date (later to upcoming)', 'qrk' ) }}
						</x-form.option>
						<x-form.option value="price-low" label="{{ __( 'Price (low to high)', 'qrk' )  }}">
							{{ __( 'Price (low to high)', 'qrk' ) }}
						</x-form.option>
						<x-form.option value="price-high" label="{{ __( 'Price (high to low)', 'qrk' )  }}">
							{{ __( 'Price (high to low)', 'qrk' ) }}
						</x-form.option>
					</x-form.select>
				</x-form.field>
			</quark-expedition-search-filters>
		</x-modal.body>
		<x-modal.footer>
			<x-expedition-search.sidebar-filters.cta-clear />
			<x-expedition-search.sidebar-filters.cta-view-results />
		</x-modal.footer>
	</x-modal>
</quark-expedition-search-sticky-filters>
