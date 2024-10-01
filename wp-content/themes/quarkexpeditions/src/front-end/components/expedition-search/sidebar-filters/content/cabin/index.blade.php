@props( [
	'filters_data' => [],
] )

@php
	if ( empty( $filters_data ) ) {
		return;
	}
@endphp

<div class="expedition-search__sidebar-filters-content-cabin">
	<h5 class="h5 expedition-search__sidebar-filters-content-title">{{ __( 'Cabin', 'qrk' ) }}</h5>
	<x-accordion>
		<x-expedition-search.sidebar-filters.content.cabin.cabin-classes :cabin_classes="$filters_data['cabin_classes']" />
		<x-expedition-search.sidebar-filters.content.cabin.travelers :travelers="$filters_data['travelers']" />
	</x-accordion>
</div>
