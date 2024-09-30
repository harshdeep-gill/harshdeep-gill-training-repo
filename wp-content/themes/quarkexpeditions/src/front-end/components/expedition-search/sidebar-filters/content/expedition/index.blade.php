@props( [
	'filters_data' => [],
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}
@endphp

<div class="expedition-search__sidebar-filters-content-expedition">
	<h5 class="h5 expedition-search__sidebar-filters-content-title">{{ __( 'Expedition', 'qrk' ) }}</h5>
	<x-accordion>
		@foreach ( $filters_data as $key => $filter_data )
			@if ( ! empty( $filters_data[ $key ] ) )
				<x-accordion.item>
					<x-accordion.item-handle title="{{ $key }}" />
					<x-accordion.item-content>
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Magnis dis parturient montes nascetur ridiculus mus mauris. Pharetra pharetra massa massa ultricies mi quis hendrerit dolor. Aliquam nulla facilisi cras fermentum odio. Dolor sit amet consectetur adipiscing elit pellentesque habitant.</p>
					</x-accordion.item-content>
				</x-accordion.item>
			@endif
		@endforeach
	</x-accordion>
</div>
